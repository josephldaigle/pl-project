<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/13/18
 */


namespace PapaLocal\Controller\Api;


use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\ReferralAgreement\Exception\RemoveInviteeException;
use PapaLocal\ReferralAgreement\Form\Invitee\RemoveInviteeForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateAgreementStatusForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateDescriptionForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateLocationsForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateQuantityForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateReferralPriceForm;
use PapaLocal\ReferralAgreement\Form\Agreement\UpdateStrategyForm;
use PapaLocal\ReferralAgreement\Form\CreateAgreementForm;
use PapaLocal\ReferralAgreement\Form\Invitee\InvitationResponseForm;
use PapaLocal\ReferralAgreement\Form\UpdateAgreementNameForm;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PapaLocal\ReferralAgreement\Workflow\Agreement\PublishGuardBlockCode;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\Exception\TransitionException;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Exception\AgreementExistsException;
use PapaLocal\ReferralAgreement\Exception\InviteeExistsForAgreementException;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm;
use FOS\RestBundle\Controller\FOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Class ReferralAgreementController.
 *
 * @package PapaLocal\Controller\Api
 */
class ReferralAgreementController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ReferralAgreementController constructor.
     *
     * @param MessageBusInterface $applicationBus
     * @param MessageFactory      $messageFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(
        MessageBusInterface $applicationBus,
        MessageFactory $messageFactory,
        LoggerInterface $logger
    )
    {
        $this->applicationBus = $applicationBus;
        $this->messageFactory = $messageFactory;
        $this->logger = $logger;
    }

    /**
     * Create a referral agreement for a user.
     *
     * @Rest\Post("/agreement/add")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\CreateAgreementForm",
     *     converter="PapaLocal\ReferralAgreement\Form\CreateAgreementConverter")
     *
     * @param Request                                          $request
     * @param ConstraintViolationListInterface                 $validationErrors
     * @param CreateAgreementForm                              $form
     * @param \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory
     * @param GuidGeneratorInterface                           $guidGenerator
     * @param TokenStorageInterface                            $tokenStorage
     *
     * @return JsonResponse
     */
    public function saveReferralAgreementBasicDetail(
        Request $request,
        ConstraintViolationListInterface $validationErrors,
        CreateAgreementForm $form,
        \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory,
        GuidGeneratorInterface $guidGenerator,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('createReferralAgreement', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // fetch current user
            $user = $tokenStorage->getToken()->getUser();

            // generate guid
            $agreementId = $guidGenerator->generate();

            // load user's company
            $findCompanyQuery = $iaMessageFactory->newFindCompanyByUserGuid($user->getGuid());
            $company = $this->applicationBus->dispatch($findCompanyQuery);

            // create agreement
            // TODO: Replace with session selection when CompanySelector implemented
            $command = $this->messageFactory->newCreateReferralAgreement($agreementId, $form, $user->getGuid(),
                $company->getGuid());
            $this->applicationBus->dispatch($command);

            // return success message
            // no nextForm property, because createAgreement always leads to addInvitee
            return new JsonResponse(array(
                'message' => 'Your agreement has been created, but still needs to be published. Complete each step to finish.',
                'agreementId' => $agreementId->value(),
            ), JsonResponse::HTTP_OK);

        } catch (AgreementExistsException $agreementExistsException) {
            // a referral agreement already exists for the name given
            return new JsonResponse(array('validationErrors' => 'You already have an agreement with that name.'),
                JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Adds an invitee during initial agreement creation.
     *
     * @Rest\Post("/agreement/invitee/add")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm",
     *                         converter="PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeConverter")
     *
     * @param Request                          $request
     * @param ReferralAgreementInviteeForm     $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param GuidGeneratorInterface           $guidGenerator
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return JsonResponse
     */
    public function addInvitee(
        Request $request,
        ReferralAgreementInviteeForm $form,
        ConstraintViolationListInterface $validationErrors,
        GuidGeneratorInterface $guidGenerator,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('addReferralAgreementInvitee', $request);

            // validate form inputs
            if (!$request->request->has('discardContinue')) {
                if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                    return $response;
                }
            }

            // fetch current user
            $user = $tokenStorage->getToken()->getUser();

            // make sure user does not add himself as invitee
            if ($form->getEmailAddress()->getEmailAddress() === $user->getUsername()) {
                return new JsonResponse(array('validationErrors' => 'You cannot add yourself as an invitee.'),
                    JsonResponse::HTTP_BAD_REQUEST);
            }

            // load the agreement
            $findAgmtQry = $this->messageFactory->newFindAgreementByGuid($form->getAgreementId());
            $referralAgreement = $this->applicationBus->dispatch($findAgmtQry);

            // handle user discard/continue when no invitees exist
            if ($request->request->has('discardContinue')) {

                if ($referralAgreement->getInvitees()->count() < 1) {
                    // cannot continue, because there are no invitees
                    // show validation error
                    return new JsonResponse(array(
                        'validationErrors' => 'You need to save at least one invitation in order to continue.',
                    ), JsonResponse::HTTP_BAD_REQUEST);

                } else {

                    /*
                     * user has clicked discard/continue on addInvitee form, and the agreement has invitees
                     */

                    // try to publish agreement - will trigger notifications to invitee(s)
                    $publishCommand = $this->messageFactory->newPublishAgreement($referralAgreement->getGuid());
                    $this->applicationBus->dispatch($publishCommand);

                    // return response
                    return new JsonResponse(array(
                        'message' => 'Your agreement has been published, and invitations sent! You should start seeing your invitees joining soon!',
                        'agreementId' => $form->getAgreementId()->value(),
                    ), JsonResponse::HTTP_OK);
                }
            }

            // add the invitee to the database
            // new command for adding invitee
            $inviteeGuid = $guidGenerator->generate();

            $saveInviteeCmd = $this->messageFactory->newSaveAgreementInvitee($inviteeGuid, $form);
            $this->applicationBus->dispatch($saveInviteeCmd);

            // reload the agreement with the new invitee included
            $referralAgreement = $this->applicationBus->dispatch($findAgmtQry);

            // handle user selected 'Save + New Invitee'
            if (!$request->request->has('isLast')) {
                // more invitees to add, display form again
                return new JsonResponse(array(
                    'message' => sprintf('An invitation will be sent to %s once this agreement is activated.',
                        $form->getEmailAddress()->getEmailAddress()),
                    'nextForm' => 'addReferralAgreementInvitee',
                ), JsonResponse::HTTP_OK);
            }

            // no more invitees to add, try to publish the agreement
            // user selected 'Save + Continue'

            // trigger publish workflow
            $publishCommand = $this->messageFactory->newPublishAgreement($referralAgreement->getGuid());
            $this->applicationBus->dispatch($publishCommand);

            return new JsonResponse(array(
                'message' => sprintf('%s has been added, and will receive an invitation when the agreement is published.',
                    $form->getEmailAddress()->getEmailAddress()),
                JsonResponse::HTTP_OK,
            ));

        } catch (InviteeExistsForAgreementException $ieae) {
            // the invitee being added has already been assigned to the agreement

            // return error response
            return new JsonResponse(array('message' => 'You\'ve already invited someone with that email address.'),
                JsonResponse::HTTP_BAD_REQUEST);

        } catch (NotEnabledTransitionException $notEnabledTransitionException) {

            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s',
                $notEnabledTransitionException->getLine(), $notEnabledTransitionException->getFile(),
                $notEnabledTransitionException->getMessage()), array(
                $notEnabledTransitionException,
                $notEnabledTransitionException->getSubject(),
                $notEnabledTransitionException->getTransitionBlockerList(),
            ));

            foreach ($notEnabledTransitionException->getTransitionBlockerList() as $blocker) {
                switch ($blocker->getCode()) {
                    case PublishGuardBlockCode::BLOCK_PAY_METHOD:
                        return new JsonResponse(array(
                            'message' => 'Please add a payment method to your account, so that you can pay for referrals.',
                            'nextForm' => 'addPaymentAccount',
                            'agreementId' => $request->request->get('agreementId'),
                        ), JsonResponse::HTTP_OK);
                        break;

                    case PublishGuardBlockCode::BLOCK_ACCT_BAL:
                        return new JsonResponse(array(
                            'message' => sprintf('%s Please deposit $%0.2f to publish this agreement.',
                                $blocker->getMessage(),
                                ($blocker->getParameters()['requiredBalance'] - $blocker->getParameters()['currentBalance'])),
                            'nextForm' => 'addFunds',
                            'agreementId' => $request->request->get('agreementId'),
                        ), JsonResponse::HTTP_OK);
                        break;
                    // no invitee case is handled in controller logic
                    default:
                        break;
                }
            }

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (TransitionException $transitionException) {

            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s',
                $transitionException->getLine(), $transitionException->getFile(), $transitionException->getMessage()),
                array($transitionException));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Adds an invitee from the feed.
     *
     * The primary difference between this action and addInvitee is that this action does
     * not automatically attempt to publish the agreement.
     *
     * @Rest\Post("/agreement/invitee/add")
     * @ParamConverter("form", class="PapaLocal\ValueObject\Form\Agreement\ReferralAgreementInviteeForm",
     *     converter="PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeConverter")
     *
     * @param Request                          $request
     * @param ReferralAgreementInviteeForm     $form
     * @param GuidGeneratorInterface           $guidGenerator
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return JsonResponse
     */
    public function addInviteeFromFeed(
        Request $request,
        ReferralAgreementInviteeForm $form,
        GuidGeneratorInterface $guidGenerator,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('addReferralAgreementInvitee', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // fetch current user
            $user = $tokenStorage->getToken()->getUser();

            // make sure user does not add himself as invitee
            if ($form->getEmailAddress()->getEmailAddress() === $user->getUsername()) {
                return new JsonResponse(array('validationErrors' => 'You cannot add yourself as an invitee.'),
                    JsonResponse::HTTP_BAD_REQUEST);
            }

            // add the invitee
            $inviteeGuid = $guidGenerator->generate();

            $saveInviteeCmd = $this->messageFactory->newSaveAgreementInvitee($inviteeGuid, $form);
            $this->applicationBus->dispatch($saveInviteeCmd);

            // if user selected 'Save + New Invitee', show the invitee form again.
            if ($request->request->has('isLast') && (true == $request->request->get('isLast'))) {
                // no more invitees to add
                return new JsonResponse(array(
                    'message' => sprintf('An invitation will be sent to %s when this agreement is activated.',
                        $form->getEmailAddress()->getEmailAddress()),
                ), JsonResponse::HTTP_OK);

            } else {

                // user would like to add more invitees, include nextForm var in response
                return new JsonResponse(array(
                    'message' => sprintf('An invitation will be sent to %s when this agreement is activated.',
                        $form->getEmailAddress()->getEmailAddress()),
                    'nextForm' => 'addReferralAgreementInvitee',
                ), JsonResponse::HTTP_OK);
            }

        } catch (InviteeExistsForAgreementException $iee) {

            return new JsonResponse(array(
                'validationErrors' => 'You\'ve already sent an invitation to someone with that email address.',
            ), JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/invitee/remove")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Invitee\RemoveInviteeForm", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param RemoveInviteeForm                $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function removeInvitee(Request $request,
                                  RemoveInviteeForm $form,
                                  ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('removeAgreementInvitee', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // invoke application
            $this->applicationBus->dispatch($form);

            // return success message
            return new JsonResponse(array(
                'message' => 'The invitee has been removed.',
            ), JsonResponse::HTTP_OK);

        } catch (RemoveInviteeException $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            return new JsonResponse(array(
                'validationErrors' => ['You must keep least 1 participant on the agreement. Try adding another participant before tyring to remove this one.']
            ), JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/name/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\UpdateAgreementNameForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateAgreementNameForm          $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateName(
        Request $request,
        UpdateAgreementNameForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementName', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $command = $this->messageFactory->newUpdateName(new Guid($form->getGuid()), $form->getName());
            $this->applicationBus->dispatch($command);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement name updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/description/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateDescriptionForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateDescriptionForm            $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateDescription(
        Request $request,
        UpdateDescriptionForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementDescription', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement name
            $updateDescCmd = $this->messageFactory->newUpdateAgreementDescription(new Guid($form->getAgreementGuid()),
                $form->getDescription());
            $this->applicationBus->dispatch($updateDescCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement description updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/status/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateAgreementStatusForm",
     *     converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateAgreementStatusForm        $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param GuidGeneratorInterface           $guidGenerator
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return JsonResponse
     */
    public function updateStatus(
        Request $request,
        UpdateAgreementStatusForm $form,
        ConstraintViolationListInterface $validationErrors,
        GuidGeneratorInterface $guidGenerator,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementStatus', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // fetch user's company
            $user = $tokenStorage->getToken()->getUser();

            // dispatch command to app
            switch ($form->getStatus()) {
                case Status::ACTIVE():

                    $updateStatusCmd = $this->messageFactory->newActivateAgreement($form->getAgreementGuid(),
                        StatusChangeReason::OWNER_REQUESTED()->getValue(), $user->getGuid()->value());

                    break;
                case Status::INACTIVE():
                    $updateStatusCmd = $this->messageFactory->newPauseAgreement($guidGenerator->createFromString($form->getAgreementGuid()),
                        StatusChangeReason::OWNER_REQUESTED(), $user->getGuid());

                    break;
                default: // handle unknown cases
                    return new JsonResponse(array('message' => 'The value provided for status field is not valid.'),
                        JsonResponse::HTTP_BAD_REQUEST);
            }
            $this->applicationBus->dispatch($updateStatusCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement status updated.',
            ), JsonResponse::HTTP_OK);
        } catch (NotEnabledTransitionException $exception) {

            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('validationErrors' => [$exception->getTransitionBlockerList()->getIterator()->current()->getMessage()]),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array(
                $exception,
                ($exception instanceof NotEnabledTransitionException) ? $exception->getTransitionBlockerList() : null,
            ));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/quantity/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateQuantityForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateQuantityForm               $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateQuantity(
        Request $request,
        UpdateQuantityForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementQuantity', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement name
            $updateQtyCmd = $this->messageFactory->newUpdateAgreementQuantity($form->getAgreementGuid(),
                $form->getQuantity());
            $this->applicationBus->dispatch($updateQtyCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement quantity updated.',
            ), JsonResponse::HTTP_OK);

        } catch (TransitionException $transitionException) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $transitionException->getLine(),
                $transitionException->getFile(), $transitionException->getMessage()), array($transitionException));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/strategy/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateStrategyForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateStrategyForm               $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return JsonResponse
     */
    public function updateStrategy(
        Request $request,
        UpdateStrategyForm $form,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementStrategy', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // fetch user's company
            $user = $tokenStorage->getToken()->getUser();

            // update agreement strategy
            $updateStrategyCmd = $this->messageFactory->newUpdateAgreementStrategy($form->getAgreementGuid(),
                $form->getStrategy());
            $this->applicationBus->dispatch($updateStrategyCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement strategy updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/price/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateReferralPriceForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateReferralPriceForm          $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updatePrice(
        Request $request,
        UpdateReferralPriceForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementPrice', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement price
            $this->applicationBus->dispatch($form);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement price updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/location/include/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateLocationsForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateLocationsForm              $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateIncludedLocations(
        Request $request,
        UpdateLocationsForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('updateReferralAgreementLocation', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $form->setContext('include');

            // update agreement locations
            $this->applicationBus->dispatch($form);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement locations updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/location/include/update")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Agreement\UpdateLocationsForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateLocationsForm              $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateExcludedLocations(
        Request $request,
        UpdateLocationsForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('updateReferralAgreementLocation', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement locations
            $form->setContext('exclude');

            // update agreement locations
            $this->applicationBus->dispatch($form);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement locations updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @Rest\Post("/agreement/referral/service/include/update")
     * @ParamConverter("referralAgreement", class="PapaLocal\ReferralAgreement\Entity\ReferralAgreement",
     *     converter="PapaLocal\ParamConverter\ReferralAgreementParamConverter",
     *     options={"validator"={"groups"={"update_included_services", "update"}}})
     *
     * @param Request                          $request
     * @param ReferralAgreement                $referralAgreement
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateIncludedServices(
        Request $request,
        ReferralAgreement $referralAgreement,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('updateReferralAgreementService', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement name
            // TODO: Implement message
            //			$agreementRepository->updateIncludedServices($referralAgreement);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement services updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/service/exclude/update")
     * @ParamConverter("referralAgreement", class="PapaLocal\ReferralAgreement\Entity\ReferralAgreement",
     *     converter="PapaLocal\ParamConverter\ReferralAgreementParamConverter",
     *     options={"validator"={"groups"={"update_excluded_services", "update"}}})
     *
     * @param Request                          $request
     * @param ReferralAgreement                $referralAgreement
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateExcludedServices(
        Request $request,
        ReferralAgreement $referralAgreement,
        ConstraintViolationListInterface $validationErrors
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('updateReferralAgreementService', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update agreement name
            // TODO: Implement message
            //			$agreementRepository->updateExcludedServices($referralAgreement);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement services updated.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/invitation/accept")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Invitee\InvitationResponseForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                 $request
     * @param ConstraintViolationList $validationErrors
     * @param InvitationResponseForm  $form
     *
     * @return JsonResponse
     */
    public function acceptInvitation(
        Request $request,
        ConstraintViolationList $validationErrors,
        InvitationResponseForm $form
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('joinReferralAgreement', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // assign invitee to agreement as participant
            $acceptCmd = $this->messageFactory->newAcceptInvitation(new Guid($form->getAgreementGuid()),
                new Guid($form->getInviteeGuid()));
            $this->applicationBus->dispatch($acceptCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'Agreement joined! You can now start sending referrals to that agreement.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/invitation/decline")
     * @ParamConverter("form", class="PapaLocal\ReferralAgreement\Form\Invitee\InvitationResponseForm",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param ConstraintViolationListInterface $validationErrors
     * @param InvitationResponseForm           $form
     * @param GuidFactory                      $guidFactory
     *
     * @return JsonResponse
     */
    public function declineInvitation(
        Request $request,
        ConstraintViolationListInterface $validationErrors,
        InvitationResponseForm $form,
        GuidFactory $guidFactory
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('declineReferralAgreementInvitation', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // add invitee to agreement
            $declineCmd = $this->messageFactory->newDeclineInvitation(
                $guidFactory->createFromString($form->getAgreementGuid()),
                $guidFactory->createFromString($form->getInviteeGuid()));
            $this->applicationBus->dispatch($declineCmd);

            // return success message
            return new JsonResponse(array(
                'message' => 'You have declined to join this agreement. It will no longer show in your feed, unless you receive a new invitation.',
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/agreement/referral/publish")
     *
     * @param Request               $request
     * @param TokenStorageInterface $tokenStorage
     *
     * @return JsonResponse
     */
    public function publish(
        Request $request,
        TokenStorageInterface $tokenStorage
    )
    {

        try {
            // validate CSRF token
            $this->validateFormToken('publishAgreement', $request);

            $user = $tokenStorage->getToken()->getUser();

            $agreementId = new Guid($request->request->get('agreementId'));
            $publishCommand = $this->messageFactory->newPublishAgreement($agreementId);
            $this->applicationBus->dispatch($publishCommand);

            // return a response
            return new JsonResponse(array(
                'message' => 'You agreement has been published!',
            ), JsonResponse::HTTP_OK);


        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/agreement/strategy/renew")
     * @return JsonResponse
     */
    public function renewStrategy()
    {
        try {
            $command = $this->messageFactory->newRenewStrategy();
            $this->applicationBus->dispatch($command);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));
        }

        // return success message
        return new JsonResponse(array(
            'message' => 'Strategies successfully renewed.',
        ), JsonResponse::HTTP_OK);
    }
}