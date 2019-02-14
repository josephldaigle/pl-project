<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/23/18
 */

namespace PapaLocal\Controller\Api;


use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Referral\Form\DisputeResolution;
use PapaLocal\Referral\Form\ReferralForm;
use PapaLocal\Referral\Form\ReferralRate;
use PapaLocal\Referral\Message\MessageFactory;
use PapaLocal\Referral\Workflow\ReferralGuardBlockCode;
use PapaLocal\Response\RestResponseMessage;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;


/**
 * Class ReferralController.
 *
 * @package PapaLocal\Controller\Api
 */
class ReferralController extends FOSRestController
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
     * ReferralController constructor.
     * @param MessageBusInterface $applicationBus
     * @param MessageFactory $messageFactory
     */
    public function __construct(MessageBusInterface $applicationBus, MessageFactory $messageFactory)
    {
        $this->applicationBus = $applicationBus;
        $this->messageFactory = $messageFactory;
    }


    /**
     * @Rest\Post("/referral/add")
     * @ParamConverter("form", class="PapaLocal\Referral\Form\ReferralForm",
     *     converter="PapaLocal\Referral\ParamConverter\ReferralFormConverter")
     *
     * Validation groups for this action are managed by the param converter.
     *
     * @param Request $request
     * @param ReferralForm $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function createReferral(Request $request,
                                   ReferralForm $form,
                                   ConstraintViolationListInterface $validationErrors,
                                   TokenStorageInterface $tokenStorage,
                                   LoggerInterface $logger)
    {
        try {
            // validate CSRF token
            $this->validateFormToken('addReferral', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response){ return $response; }

            $user = $tokenStorage->getToken()->getUser();

            $command = $this->messageFactory->newCreateReferral($form, $user->getGuid());
            $this->applicationBus->dispatch($command);

            // return success message
            return new JsonResponse(array(
                'message' => 'You just created a referral!',
            ), JsonResponse::HTTP_OK);

        } catch (NotEnabledTransitionException $exception) {
            $logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            $list = $exception->getTransitionBlockerList();
            $blockerCode  = $list->getIterator()->getArrayCopy()[0]->getCode();

            switch ($blockerCode) {
                case ReferralGuardBlockCode::INACTIVE_AGREEMENT:
                    // return error response
                    return new JsonResponse(array(
                        'message' => 'The agreement you are sending this referral to is inactive.',
                    ), JsonResponse::HTTP_BAD_REQUEST);
                    break;

                case ReferralGuardBlockCode::AGREEMENT_QUOTA:
                    return new JsonResponse(array(
                        'message' => 'The agreement you are sending this referral to is no longer accepting referrals.',
                    ), JsonResponse::HTTP_BAD_REQUEST);

                case ReferralGuardBlockCode::CONTACT_IS_USER:
                    return new JsonResponse(array(
                        'message' => 'The contact you are sending this referral to is a user of PapaLocal, and will need to invite you into an agreement.',
                    ), JsonResponse::HTTP_BAD_REQUEST);
                    break;

                default:
                    return new JsonResponse(array(
                        'message' => 'An error occured during the creation of this referral.',
                    ), JsonResponse::HTTP_BAD_REQUEST);
            }

        } catch (\Exception $exception) {
            $logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/referral/rate")
     *
     * @ParamConverter("form", class="PapaLocal\Referral\Form\ReferralRate",
     *     converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ReferralRate $form
     * @param TokenStorageInterface $tokenStorage
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function rateReferral(Request $request,
                                 LoggerInterface $logger,
                                 ReferralRate $form,
                                 TokenStorageInterface $tokenStorage,
                                 ConstraintViolationListInterface $validationErrors)
    {
        try {
            // validate CSRF token
            $this->validateFormToken('rateReferral', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response){ return $response; }

            $command = $this->messageFactory->newRateReferral($form);
            $this->applicationBus->dispatch($command);

            $score = $form->getReferralRate();
            $feedback = $form->getReferralFeedback();

            if ($score < 3) {

                // fetch the user
                $user = $tokenStorage->getToken()->getUser();

                return new JsonResponse(array(
                    'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                    'score' => $score,
                    'feedback' => $feedback,
                    'currentDate' => date('Y-m-d'),
                    'message' => 'You disputed this referral.'
                ), JsonResponse::HTTP_OK);

            } else {

                return new JsonResponse(array(
                    'score' => $score,
                    'feedback' => $feedback,
                    'message' => 'You rated this referral.'
                ), JsonResponse::HTTP_OK);
            }

        } catch (\Exception $exception) {
            $logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *  @Rest\Post("/referral/dispute")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @ParamConverter("form", class="PapaLocal\Referral\Form\DisputeResolution",
     *     converter="fos_rest.request_body")
     *
     * @param Request $request
     * @param DisputeResolution $form
     * @param TokenStorageInterface $tokenStorage
     * @param ConstraintViolationListInterface $validationErrors
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function resolveDispute(Request $request,
                                   DisputeResolution $form,
                                   TokenStorageInterface $tokenStorage,
                                   ConstraintViolationListInterface $validationErrors,
                                   LoggerInterface $logger)
    {
        try {
            // validate CSRF token
            $this->validateFormToken('resolveDispute', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response){ return $response; }

            // fetch the reviewer
            $reviewerGuid = $tokenStorage->getToken()->getUser()->getGuid();

            $command = $this->messageFactory->newResolveDispute($form, $reviewerGuid);
            $this->applicationBus->dispatch($command);

            // return success message
            return new JsonResponse(array(
                'resolution' => $form->getResolution(),
                'message' => 'You have resolved this dispute!'
            ), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}