<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/19/17
 * Time: 8:03 PM
 */


namespace PapaLocal\Controller\Api\Billing;


use PapaLocal\Billing\Form\CreateBankAccount;
use PapaLocal\Billing\Message\MessageFactory;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\Data\AttrType;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Exception\AuthorizeDotNetOperationException;
use PapaLocal\Entity\Exception\Data\CreditCardExistsException;
use PapaLocal\Entity\Exception\NotificationException;
use PapaLocal\Billing\Service\BillingProfileManager;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\Notification\Notifier;
use PapaLocal\Billing\Notification\AddPayMethod;
use PapaLocal\Billing\Notification\ChangePrimaryPayMethod;
use PapaLocal\Billing\Notification\DeletePayMethod;
use PapaLocal\Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * PaymentMethodsController.
 *
 * @Rest\RouteResource("/billing/account", pluralize=false)
 */
class PaymentMethodController extends FOSRestController
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
     * PaymentMethodController constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $applicationBus
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $applicationBus)
    {
        $this->messageFactory = $messageFactory;
        $this->applicationBus = $applicationBus;
    }

    /**
     * Create a new payment method (credit card).
     *
     * @Rest\Post("/billing/account/credit/add")
     *
     * @ParamConverter("creditCard", class="PapaLocal\Entity\Billing\CreditCard",  converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"create"}}})
     * @ParamConverter("address", class="PapaLocal\Entity\Address", converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"create"}}})
     *
     * @param Request                          $request
     * @param CreditCard                       $creditCard
     * @param Address                          $address
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     * @param BillingProfileManager            $billingProfileManager
     * @param Notifier                         $notifier
     * @param LoggerInterface                  $logger
     * @param SerializerInterface              $serializer
     * @param ValidatorInterface               $validator
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function saveCreditCard(Request $request,
                                   CreditCard $creditCard,
                                   Address $address,
                                   ConstraintViolationListInterface $validationErrors,
                                   TokenStorageInterface $tokenStorage,
                                   BillingProfileManager $billingProfileManager,
                                   Notifier $notifier,
                                   LoggerInterface $logger,
                                   SerializerInterface $serializer,
                                   ValidatorInterface $validator)
    {
        // validate CSRF token
        $this->validateFormToken('addCreditCard', $request);

        try {

            // validate form inputs
            $errors = new ConstraintViolationList();
            $errors->addAll($validationErrors);

            if (! $address->getId()) {
                $address->setType(AttrType::ADDRESS_BILLING);
                $errors = $validator->validate($address, null, array('create'));
            }

            $creditCard->setAddress($address);
            $errors->addAll($validator->validate($creditCard, null, array('create')));

            // handle validation errors
            if (($response = $this->handleValidationErrors($errors)) instanceof Response){ return $response; }

            // save credit card detail
            $id = $billingProfileManager->saveCreditCardForUser($tokenStorage->getToken()->getUser(), $creditCard);
            $creditCard->setId($id);

	        // notify user
		    $notification = new AddPayMethod($creditCard->getCardNumber(),
		        $tokenStorage->getToken()->getUser()->getUsername(),
		        array(
		            'accountNumber' => $creditCard->getCardNumber(),
		            'cardholder' => $creditCard->getFirstName() . ' ' . $creditCard->getLastName(),
		            'expirationDate' => $creditCard->getExpirationDate())
	               );
            $notifier->sendUserNotification($tokenStorage->getToken()->getUser()->getGuid(), $notification);

            // handle add card from 'createAgreement'
            if ($request->request->has('agreementId')) {
                // return success message
                return new JsonResponse(array(
                    'message' => 'Your card has been saved. You will need to make a deposit, to ensure you can pay for your first few referrals.',
                    'agreementId' => $request->request->get('agreementId'),
                    'nextForm' => 'addFunds',
                    'creditCard' => $serializer->normalize($creditCard, 'array'),
                ), JsonResponse::HTTP_OK);
	        }

            return new JsonResponse(array('message' => 'Payment method saved.'), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

	        $logger->error(sprintf('An error occurred while saving a user\'s credit card: %s',
		        $exception->getMessage()),
		        array(
			        'user' => $tokenStorage->getToken()->getUser()->getUsername(),
			        'creditCard' => $serializer->normalize($creditCard, 'array'),
			        'trace' => $exception->getTrace()
		        ));

        	switch (get_class($exception)) {
		        case CreditCardExistsException::class :
			        return new JsonResponse(array('message' => RestResponseMessage::ADD_CARD_DUPLICATE),
				        JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		        case AuthorizeDotNetOperationException::class :
		        	if (preg_match('/(.)+(The credit card number is invalid)(.)+/', $exception->getMessage())) {
				        return new JsonResponse(array('message' => RestResponseMessage::ADD_CARD_BAD_NUMBER),
					        JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
			        }
			        if (preg_match('/(.)+(A duplicate customer payment profile already exists)(.)+/', $exception->getMessage())) {
				        return new JsonResponse(array('message' => RestResponseMessage::ADD_CARD_DUPLICATE),
					        JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
			        }
			        return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
				        JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		        default:
			        // return error response
			        return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
				        JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
	        }
        }

    }


    /**
     * Set a payment account as the user's primary pay method.
     *
     * @Rest\Post("/billing/account/credit/primary")
     * @ParamConverter("creditCard", class="PapaLocal\Entity\Billing\CreditCard",  converter="fos_rest.request_body")
     *
     * @param Request                 $request
     * @param CreditCard              $creditCard
     * @param ConstraintViolationList $validationErrors
     * @param TokenStorageInterface   $tokenStorage
     * @param RepositoryRegistry      $repositoryRegistry
     * @param Notifier                $notifier
     * @param LoggerInterface         $logger
     *
     * @return JsonResponse
     */
    public function savePrimaryPayMethod(Request $request,
                                         CreditCard $creditCard,
                                         ConstraintViolationList $validationErrors,
                                         TokenStorageInterface $tokenStorage,
                                         RepositoryRegistry $repositoryRegistry,
                                         Notifier $notifier,
                                         LoggerInterface $logger)
    {
        // validate CSRF token
        $this->validateFormToken('primaryPayMethod', $request);

        // handle validation errors
        if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response){ return $response; }

        try {

        	// save user's new primary pay method
            $billingProfileRepository = $repositoryRegistry->get(BillingProfileRepository::class);
            $billingProfileRepository->setAsDefaultPaymentMethod($creditCard);

            $billingProfile = $billingProfileRepository->loadBillingProfile($tokenStorage->getToken()->getUser()->getId(), true);

            $ccEntity = $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId());

            // notify user
	        $notification = new ChangePrimaryPayMethod($ccEntity->getCardType(), $ccEntity->getCardNumber(), $tokenStorage->getToken()->getUser()->getUsername(), array(
	        	'accountNumber' => $ccEntity->getCardNumber(),
		        'cardholder' => $ccEntity->getFirstName() . ' ' . $ccEntity->getLastName(),
		        'expirationDate' => $ccEntity->getExpirationDate()
	        ));
	        $notifier->sendUserNotification($tokenStorage->getToken()->getUser()->getGuid(), $notification);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (NotificationException $notificationException) {

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred

            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/billing/account/credit/delete")
     * @ParamConverter("creditCard", class="PapaLocal\Entity\Billing\CreditCard", converter="fos_rest.request_body")
     *
     * @param Request                 $request
     * @param CreditCard              $creditCard
     * @param ConstraintViolationList $validationErrors
     * @param TokenStorageInterface   $tokenStorage
     * @param BillingProfileManager   $billingProfileManager
     * @param RepositoryRegistry      $repositoryRegistry
     * @param Notifier                $notifier
     * @param LoggerInterface         $logger
     *
     * @return JsonResponse
     */
    public function deleteCreditCard(Request $request,
                                     CreditCard $creditCard,
                                     ConstraintViolationList $validationErrors,
                                     TokenStorageInterface $tokenStorage,
                                     BillingProfileManager $billingProfileManager,
                                     RepositoryRegistry $repositoryRegistry,
                                     Notifier $notifier,
                                     LoggerInterface $logger)
    {
        // validate CSRF token
        $this->validateFormToken('deletePaymentMethod', $request);

        // handle validation errors
        if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response){ return $response; }

        try {
			// delete payment method
            $billingProfileRepository = $repositoryRegistry->get(BillingProfileRepository::class);
	        $billingProfile = $billingProfileRepository->loadBillingProfile($tokenStorage->getToken()->getUser()->getId(), true);

	        $cardEntity = $billingProfile->getPaymentProfile()->findBy('id', $creditCard->getId());
            $billingProfileManager->deleteCreditCard($tokenStorage->getToken()->getUser(), $creditCard);

            // notify user
            $notification = new DeletePayMethod($cardEntity->getCardNumber());
            $notifier->sendUserNotification($tokenStorage->getToken()->getUser()->getGuid(), $notification);

            return new JsonResponse(array('message' => 'Your payment method has been deleted from your account.'),
                JsonResponse::HTTP_OK);

        } catch(\Exception $exception) {
            // an exception occurred

            $logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("billing/account/checking/add")
     *
     * @param Request $request
     * @param CreateBankAccount $form
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function saveBankAccount(Request $request,
                                    CreateBankAccount $form,
                                    TokenStorageInterface $tokenStorage,
                                    LoggerInterface $logger)
    {
        try {

            // validate CSRF token
            $this->validateFormToken('createBankAccount', $request);

            $user = $tokenStorage->getToken()->getUser();

            $createCmd = $this->messageFactory->newCreateBankAccount($user, $form->getToken());
            $this->applicationBus->dispatch($createCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Payment method saved.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
