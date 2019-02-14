<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/15/17
 * Time: 8:52 PM
 */


namespace PapaLocal\Controller\Api\Billing;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Message\MessageFactory as AgreementMessageFactory;
use PapaLocal\Billing\Event\DepositCompletedEvent;
use PapaLocal\Billing\Exception\AccountNotFoundException;
use PapaLocal\Billing\Exception\DuplicateTransactionException;
use PapaLocal\Billing\Exception\ExcessiveWithdrawalAmountException;
use PapaLocal\Billing\Exception\ExcessiveWithdrawalAttemptException;
use PapaLocal\Billing\Form\DepositFunds;
use PapaLocal\Billing\Form\WithdrawFunds;
use PapaLocal\Billing\Message\MessageFactory;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Billing\Data\BillingProfileHydrator;
use PapaLocal\Core\Data\HydratorRegistry;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\Notification\Notifier;
use PapaLocal\Billing\Service\TransactionManager;
use PapaLocal\Billing\Notification\ManualDepositFail;
use PapaLocal\Billing\Notification\ManualDepositSuccess;
use FOS\RestBundle\Controller\FOSRestController;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Workflow\Registry;


/**
 * TransactionController.
 *
 * Provide actions for processing monetary transactions against a user's payment methods.
 *
 */
class TransactionController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * TransactionController constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $applicationBus
     * @param LoggerInterface $logger
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $applicationBus, LoggerInterface $logger)
    {
        $this->messageFactory = $messageFactory;
        $this->applicationBus = $applicationBus;
        $this->logger = $logger;
    }


    /**
     * Default addFunds handler.
     *
     * @Rest\Post("/billing/transaction/deposit")
     * @ParamConverter("depositForm", class="PapaLocal\Billing\Form\DepositFunds",
     *     converter="PapaLocal\Billing\Form\ParamConverter\DepositFundsParamConverter")
     *
     * @param Request                          $request
     * @param DepositFunds                     $depositForm
     * @param ConstraintViolationListInterface $validationErrors
     * @param TransactionManager               $transactionManager
     * @param HydratorRegistry                 $hydratorRegistry
     * @param EventDispatcherInterface         $dispatcher
     * @param TokenStorageInterface            $tokenStorage
     * @param SerializerInterface              $serializer
     * @param LoggerInterface                  $logger
     *
     * @return JsonResponse
     */
    public function addFunds(
        Request $request,
        DepositFunds $depositForm,
        ConstraintViolationListInterface $validationErrors,
        TransactionManager $transactionManager,
        HydratorRegistry $hydratorRegistry,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer,
        LoggerInterface $logger
    )
    {

        $user = $tokenStorage->getToken()->getUser();

        try {

            // validate CSRF token
            $this->validateFormToken('addFunds', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            // create a credit card obj
            $creditCard = $serializer->denormalize(array('id' => $depositForm->getAccountId()), CreditCard::class, 'array');

            // execute charge against user's account
            $transactionManager->chargeCreditCard($user, $creditCard, $depositForm->getAmount(),
                Transaction::DESC_DEPOSIT);

            // load billing profile after transaction complete
            // this will include the user's new balance
            $billingProfileHydrator = $hydratorRegistry->get(BillingProfileHydrator::class);
            $billingProfileHydrator->setEntity($serializer->denormalize(array('userId' => $user->getId()),
                BillingProfile::class, 'array'));
            $billingProfile = $billingProfileHydrator->hydrate();

            $cardEntity = $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId());

            // dispatch deposit completed event
            $dispatcher->dispatch(DepositCompletedEvent::class, new DepositCompletedEvent($user, $cardEntity, $depositForm->getAmount(), $billingProfile->getBalance()));

            return new JsonResponse(array(
                'message' => sprintf('%.2f has been deposited to your account.', $depositForm->getAmount()),
                JsonResponse::HTTP_OK,
            ));

        } catch (AccountNotFoundException $anfe) {

            // log error
            $logger->error(sprintf('User %s attempted to make a deposit using an account that was not found.', $user->getUsername()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::CHARGE_ACCOUNT_FAIL),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (DuplicateTransactionException $dte) {

            // save failed transaction
            $logger->critical(sprintf('Unable to process deposit for %s', $user->getUsername()), array($dte));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::DUPLICATE_TRANSACTION),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $exception) {
            // log error
            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Deposit funds in a user's account to create a referral agreement.
     *
     * This is the last step in the process of 'creating a referral agreement'.
     * Attempt to publish the agreement.
     *
     * @Rest\Post("/billing/transaction/deposit")
     * @ParamConverter("depositForm", class="PapaLocal\Billing\Form\DepositFunds",
     *     converter="PapaLocal\Billing\Form\ParamConverter\DepositFundsParamConverter")
     *
     * @param Request                          $request
     * @param DepositFunds                     $depositForm
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     * @param ReferralAgreementRepository      $referralAgreementRepository
     * @param AgreementMessageFactory          $raFactory
     * @param RepositoryRegistry               $repositoryRegistry
     * @param HydratorRegistry                 $hydratorRegistry
     * @param Registry                         $workflowRegistry
     * @param EventDispatcherInterface         $dispatcher
     * @param LoggerInterface                  $logger
     * @param SerializerInterface              $serializer
     * @param TransactionManager               $transactionManager
     *
     * @return JsonResponse
     */
    public function addFundsToCreateAgreement(
        Request $request,
        DepositFunds $depositForm,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage,
        AgreementMessageFactory $agreementMessageFactory,
        ReferralAgreementRepository $referralAgreementRepository,
        RepositoryRegistry $repositoryRegistry,
        AgreementMessageFactory $raFactory,
        HydratorRegistry $hydratorRegistry,
        Registry $workflowRegistry,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        TransactionManager $transactionManager
    )
    {
        try {
            $user = $tokenStorage->getToken()->getUser();

            // validate CSRF token
            $this->validateFormToken('addFunds', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            // create a credit card obj
            $creditCard = $serializer->denormalize(array('id' => $depositForm->getAccountId()), CreditCard::class, 'array');

            // execute charge against user's account
            $transactionManager->chargeCreditCard($user, $creditCard, $depositForm->getAmount(),
                Transaction::DESC_DEPOSIT);

            // load billing profile after transaction complete
            $billingProfileHydrator = $hydratorRegistry->get(BillingProfileHydrator::class);
            $billingProfileHydrator->setEntity($serializer->denormalize(array('userId' => $user->getId()),
                BillingProfile::class, 'array'));
            $billingProfile = $billingProfileHydrator->hydrate();

            $cardEntity = $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId());

            // dispatch deposit completed event
            $dispatcher->dispatch(DepositCompletedEvent::class, new DepositCompletedEvent($user, $cardEntity, $depositForm->getAmount(), $billingProfile->getBalance()));

        } catch (AccountNotFoundException $anfe) {

            // log error
            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($anfe),
                $anfe->getMessage(), $anfe->getFile(), $anfe->getLine()), array('exception' => $anfe, 'trace' => $anfe->getTrace()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::CHARGE_ACCOUNT_FAIL),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (DuplicateTransactionException $dte) {

            // log failed transaction
            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($dte),
                $dte->getMessage(), $dte->getFile(), $dte->getLine()), array('exception' => $dte, 'trace' => $dte->getTrace()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::DUPLICATE_TRANSACTION),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $exception) {
            // log error
            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array('exception' => $exception, 'trace' => $exception->getTrace()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        try {
            // publish the agreement
            if ($request->request->has('agreementId')) {

                // try to publish the agreement
                $loadAgmtCmd = $raFactory->newFindAgreementByGuid(new Guid($request->request->get('agreementId')));
                $referralAgreement = $this->applicationBus->dispatch($loadAgmtCmd);

                $workflow = $workflowRegistry->get($referralAgreement);

                if ($workflow->can($referralAgreement, 'publish')) {
                    $workflow->apply($referralAgreement, 'publish');
                } else {
                    // could still have inadequate balance
                    $blockers = $workflow->buildTransitionBlockerList($referralAgreement, 'publish');

                    if (false === $blockers->isEmpty()) {
                        return new JsonResponse(array(
                            'validationErrors' => sprintf('You need to deposit at least 3 times the amount of a referral ($%.2f), as described on your agreement. Min deposit: ($%.2f)',
                                $referralAgreement->getBid(), $referralAgreement->getBid() * 3),
                        ), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }

            // return response
            return new JsonResponse(array(
                'message' => sprintf('%.2f has been deposited to your account, and your agreement has been published', $depositForm->getAmount()),
                JsonResponse::HTTP_OK,
            ));

        } catch (\Exception $exception) {
            // log error
            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array('exception' => $exception, 'trace' => $exception->getTrace()));

            return new JsonResponse(array(
                'message' => sprintf('%.2f has been deposited to your account, but your agreement could not be published. Please contact technical support to publish the agreement.', $depositForm->getAmount()),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            ));
        }
    }

    /**
     * Deposit funds in a user's account to publish a referral agreement from feed.
     *
     * @Rest\Post("/billing/transaction/deposit")
     * @ParamConverter("depositForm", class="PapaLocal\Billing\Form\DepositFunds",
     *     converter="PapaLocal\Billing\Form\ParamConverter\DepositFundsParamConverter")
     *
     * @param Request                          $request
     * @param DepositFunds                     $depositForm
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     * @param ReferralAgreementRepository      $referralAgreementRepository
     * @param HydratorRegistry                 $hydratorRegistry
     * @param Registry                         $workflowRegistry
     * @param SerializerInterface              $serializer
     * @param TransactionManager               $transactionManager
     * @param Notifier                         $notifier
     *
     * @return JsonResponse
     * @throws \PapaLocal\Entity\Exception\NotificationException
     */
    public function addFundsFromFeedForReferralAgreement(
        Request $request,
        DepositFunds $depositForm,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage,
        AgreementMessageFactory $raFactory,
        HydratorRegistry $hydratorRegistry,
        Registry $workflowRegistry,
        SerializerInterface $serializer,
        TransactionManager $transactionManager,
        Notifier $notifier
    )
    {
        try {
            // should return a next form
            // validate CSRF token
            $this->validateFormToken('addFunds', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            $user = $tokenStorage->getToken()->getUser();

            // load billing profile after transaction complete
            $billingProfileHydrator = $hydratorRegistry->get(BillingProfileHydrator::class);
            $billingProfileHydrator->setEntity($serializer->denormalize(array('userId' => $user->getId()),
                BillingProfile::class, 'array'));
            $billingProfile = $billingProfileHydrator->hydrate();

            // create a credit card obj
            $creditCard = $serializer->denormalize(array('id' => $depositForm->getAccountId()), CreditCard::class,
                'array');

            // execute charge against user's account
            $transactionManager->chargeCreditCard($user, $creditCard, $depositForm->getAmount(),
                Transaction::DESC_DEPOSIT);

            // publish the agreement
            if ($request->request->has('agreementId')) {
                // try to publish the agreement
                $loadAgmtCmd = $raFactory->newFindAgreementByGuid(new Guid($request->request->get('agreementId')));
                $referralAgreement = $this->applicationBus->dispatch($loadAgmtCmd);

                $workflow = $workflowRegistry->get($referralAgreement);

                if ($workflow->can($referralAgreement, 'publish')) {
                    dump($workflow);
                    $workflow->apply($referralAgreement, 'publish');
                }
//                else {
//                    // could still have inadequate balance
//                    $blockers = $workflow->buildTransitionBlockerList($referralAgreement, 'publish');
//                    dump($blockers);
//                    if ( ! $blockers->isEmpty()) {
//                        dump($blockers);
//                        return new JsonResponse(array(
//                            'validationErrors' => sprintf('You need to deposit at least 3 times the amount of a referral ($%.2f), as described on your agreement. Min deposit: ($%.2f)',
//                                $referralAgreement->getBid(), $referralAgreement->getBid() * 3),
//                        ),
//                            JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
//                    }
//                }
            }

            $billingProfileHydrator = $hydratorRegistry->get(BillingProfileHydrator::class);
            $billingProfileHydrator->setEntity($serializer->denormalize(array('userId' => $user->getId()),
                BillingProfile::class, 'array'));
            $billingProfile = $billingProfileHydrator->hydrate();

            $cardEntity = $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId());

            // notify user of deposit
            $notification = new ManualDepositSuccess($depositForm->getAmount(), $billingProfile->getBalance(),
                $user->getUsername(), array(
                    'accountNumber'  => $cardEntity->getCardNumber(),
                    'cardholder'     => $cardEntity->getFirstName().' '.$cardEntity->getLastName(),
                    'expirationDate' => $cardEntity->getExpirationDate(),
                    'depositAmount'  => $depositForm->getAmount(),
                    'accountBalance' => $billingProfile->getBalance(),
                ));
            $notifier->sendUserNotification($user->getGuid(), $notification);

            return new JsonResponse(array(
                'message' => sprintf('%.2f has been deposited to your account.', $depositForm->getAmount()),
                JsonResponse::HTTP_OK,
            ));

        } catch (AccountNotFoundException $anfe) {
            // log error
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $anfe->getLine(), $anfe->getFile(), $anfe->getMessage()), array('exception' => $anfe, 'context' => $anfe->getTrace()));

            // notify user
            $notification = new ManualDepositFail($depositForm->getAmount(), $billingProfile->getBalance(),
                $user->getUsername(), array(
                    'accountNumber'  => $cardEntity->getCardNumber(),
                    'cardholder'     => $cardEntity->getFirstName().' '.$cardEntity->getLastName(),
                    'expirationDate' => $cardEntity->getExpirationDate(),
                    'depositAmount'  => $depositForm->getAmount(),
                    'accountBalance' => $billingProfile->getBalance(),
                ));
            $notifier->sendUserNotification($user->getGuid(), $notification);

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::CHARGE_ACCOUNT_FAIL),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (DuplicateTransactionException $dte) {

            // log failed transaction
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $dte->getLine(), $dte->getFile(), $dte->getMessage()), array('exception' => $dte, 'context' => $dte->getTrace()));

            // notify user
            $notification = new ManualDepositFail($depositForm->getAmount(), $billingProfile->getBalance(),
                $user->getUsername(), array(
                    'accountNumber'  => $cardEntity->getCardNumber(),
                    'cardholder'     => $cardEntity->getFirstName().' '.$cardEntity->getLastName(),
                    'expirationDate' => $cardEntity->getExpirationDate(),
                    'depositAmount'  => $depositForm->getAmount(),
                    'accountBalance' => $billingProfile->getBalance(),
                ));
            $notifier->sendUserNotification($user->getGuid(), $notification);

            // return response
            return new JsonResponse(array('validationErrors' => RestResponseMessage::DUPLICATE_TRANSACTION),
                JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {
            // log error
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // notify user
            $notification = new ManualDepositFail($depositForm->getAmount(), $billingProfile->getBalance(),
                $user->getUsername(), array(
                    'accountNumber'  => $cardEntity->getCardNumber(),
                    'cardholder'     => $cardEntity->getFirstName().' '.$cardEntity->getLastName(),
                    'expirationDate' => $cardEntity->getExpirationDate(),
                    'depositAmount'  => $depositForm->getAmount(),
                    'accountBalance' => $billingProfile->getBalance(),
                ));
            $notifier->sendUserNotification($user->getGuid(), $notification);

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @Rest\Post("/billing/transaction/withdraw")
     * @ParamConverter("form", class="PapaLocal\Billing\Form\WithdrawFunds",
     *     converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param WithdrawFunds $form
     * @param TokenStorageInterface $tokenStorage
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function payoutUser(Request $request,
                               WithdrawFunds $form,
                               TokenStorageInterface $tokenStorage,
                               ConstraintViolationListInterface $validationErrors)
    {
        try {
            // should return a next form
            // validate CSRF token
            $this->validateFormToken('withdrawFunds', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $user = $tokenStorage->getToken()->getUser();

            $payoutCmd = $this->messageFactory->newPayout($form, $user->getUsername());
            $this->applicationBus->dispatch($payoutCmd);

            return new JsonResponse(array(
                'message' => sprintf('A request to withdraw %.2f has been submitted for your account. Your account will reflect your new balance immediately, and a check will be issued within 3 business days.', $form->getAmount()),
                JsonResponse::HTTP_OK,
            ));

        } catch (ExcessiveWithdrawalAmountException $amountException) {
            // log error
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($amountException),
                $amountException->getMessage(), $amountException->getFile(), $amountException->getLine()));

            return new JsonResponse(array('validationErrors' => ['The amount requested exceeds your allowed limit.']), JsonResponse::HTTP_BAD_REQUEST);

        } catch (ExcessiveWithdrawalAttemptException $attemptException) {
            // log error
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($attemptException),
                $attemptException->getMessage(), $attemptException->getFile(), $attemptException->getLine()));

            return new JsonResponse(array('validationErrors' => ['You can only withdraw once a month.']), JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {
            // log error
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            // return response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}