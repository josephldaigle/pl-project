<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/17/18
 * Time: 8:30 PM
 */

namespace PapaLocal\Controller\Api\AccountProfile;

use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Data\AttrType;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Exception\UsernameExistsException;
use PapaLocal\IdentityAccess\Form\User\UpdateAddress;
use PapaLocal\IdentityAccess\Form\User\UpdateFirstName;
use PapaLocal\IdentityAccess\Form\User\UpdateLastName;
use PapaLocal\IdentityAccess\Form\User\UpdatePhoneNumber;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\Notification\Account\ChangePassword;
use PapaLocal\Notification\Account\ChangeUsername;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\Notification\Notifier;
use PapaLocal\ValueObject\Form\ResetPassword;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * UserProfileController.
 *
 * Provides REST access to resources in the User Account Profile domain.
 *
 * @package PapaLocal\Controller\Api\AccountProfile
 */
class UserProfileController extends FOSRestController
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
     * UserProfileController constructor.
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
     * Update a user's username.
     *
     * @Rest\Post("/user/username/update")
     * @ParamConverter("emailAddress", class="PapaLocal\Entity\EmailAddress", converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"form_submit"}}})
     *
     * @param Request                          $request
     * @param EmailAddress                     $emailAddress
     * @param ConstraintViolationListInterface $validationErrors
     * @param AuthorizeDotNet                  $authNet
     * @param BillingProfileRepository         $billingProfileRepository
     * @param Notifier                         $notifier
     * @param UserRepository                   $userRepository
     * @param TokenStorageInterface            $tokenStorage
     *
     * @return bool|mixed|JsonResponse
     */
    public function updateUsername(
        Request $request,
        EmailAddress $emailAddress,
        ConstraintViolationListInterface $validationErrors,
        AuthorizeDotNet $authNet,
        BillingProfileRepository $billingProfileRepository,
        Notifier $notifier,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileUsername', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // do nothing if username has not changed
            if ($tokenStorage->getToken()->getUser()->getUsername() === $emailAddress->getEmailAddress()) {
                return new JsonResponse(array('message' => 'That was easy!'),
                    JsonResponse::HTTP_OK);
            }

            // update authnet account, if one exists
            // TODO: This should be in a worklfow in billing
            $billingProfile = $billingProfileRepository->loadBillingProfile($tokenStorage->getToken()->getUser()->getId());
            if (!is_null($billingProfile->getCustomerId())) {
                $response = $authNet->updateCustomerProfile($billingProfile->getCustomerId(),
                    $emailAddress->getEmailAddress());
            }

            // update the user's username
            $userRepository->updateUsername($tokenStorage->getToken()->getUser()->getGuid(),
                $emailAddress->getEmailAddress());

            $findByUsername = $this->messageFactory->newFindUserByUsername($emailAddress->getEmailAddress());
            $user = $this->applicationBus->dispatch($findByUsername);

            // update the user's auth token (keeps the user signed in)
            $token = new UsernamePasswordToken(
                $user, //user object with updated username
                null,
                'main',
                $tokenStorage->getToken()->getRoles());
            $tokenStorage->setToken($token);

            // notify the user
            $notifier->sendUserNotification($user->getGuid(), new ChangeUsername($user->getUsername(), array()));

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (UsernameExistsException $uee) {
            // the username chosen is already associated with an account

            $this->logger->debug(sprintf('%s tried to change their username to an email address that is already in use.',
                $emailAddress->getEmailAddress()), array($uee->getTrace()));

            return new JsonResponse(array('message' => 'That email address is already in use.'),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $exception) {
            // an exception occurred

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a user's password.
     *
     * @Rest\Post("/user/password/update")
     * @ParamConverter("resetPassword", class="PapaLocal\ValueObject\Form\ResetPassword",
     *                                  converter="fos_rest.request_body",
     *                                  options={"validator"={"groups"={"authenticated_change"}}})
     *
     * @param Request                          $request
     * @param ResetPassword                    $resetPassword
     * @param TokenStorageInterface            $tokenStorage
     * @param MessageFactory                   $iaMessageFactory
     * @param ConstraintViolationListInterface $validationErrors
     * @param Notifier                         $notifier
     *
     * @return JsonResponse
     */
    public function updatePassword(
        Request $request,
        ResetPassword $resetPassword,
        TokenStorageInterface $tokenStorage,
        MessageFactory $iaMessageFactory,
        ConstraintViolationListInterface $validationErrors,
        Notifier $notifier
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfilePassword', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // fetch current logged in user
            $user = $tokenStorage->getToken()->getUser();

            // update the user's password
            $updatePassCmd = $iaMessageFactory->newUpdatePassword($user->getGuid(), $resetPassword->getPassword());
            $this->applicationBus->dispatch($updatePassCmd);

            // notify user
            // TODO: refactor out of controller
            $notifier->sendUserNotification($user->getGuid(), new ChangePassword($user->getUsername(), array()));

            return new JsonResponse(array('message' => 'Your password has been changed!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Update a user's first name.
     *
     * @Rest\Post("/user/first-name/update")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\User\UpdateFirstName",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateFirstName                  $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateFirstName(
        Request $request,
        UpdateFirstName $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileFirstName', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the user's first name
            $updateNameCmd = $this->messageFactory->newUpdateFirstName($form->getUserGuid(), $form->getFirstName());
            $this->applicationBus->dispatch($updateNameCmd);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


    }

    /**
     * Update a user's last name.
     *
     * @Rest\Post("/user/last-name/update")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\User\UpdateLastName",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateLastName                   $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updateLastName(
        Request $request,
        UpdateLastName $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileLastName', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the user's last name
            $updateNameCmd = $this->messageFactory->newUpdateLastName($form->getUserGuid(), $form->getLastName());
            $this->applicationBus->dispatch($updateNameCmd);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a user's email address.
     *
     * @Rest\Post("/user/email-address/update")
     * @ParamConverter("emailAddress", class="PapaLocal\Entity\EmailAddress", converter="fos_rest.request_body",
     *     options={"validator"={"groups"={"form_submit"}}})
     *
     * @param Request                          $request
     * @param EmailAddress                     $emailAddress
     * @param ConstraintViolationListInterface $validationErrors
     * @param TokenStorageInterface            $tokenStorage
     * @param UserRepository                   $userRepository
     *
     * @return JsonResponse
     */
    public function updateEmailAddress(
        Request $request,
        EmailAddress $emailAddress,
        ConstraintViolationListInterface $validationErrors,
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileEmailAddress', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the user's email address
            $emailAddress->setType(AttrType::EMAIL_PRIMARY);
            $userRepository->updateEmailAddress($tokenStorage->getToken()->getUser(), $emailAddress);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a user's phone number.
     *
     * @Rest\Post("/user/phone-number/update")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\User\UpdatePhoneNumber",
     *                         converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdatePhoneNumber                $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function updatePhoneNumber(
        Request $request,
        UpdatePhoneNumber $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfilePhoneNumber', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the user's phone number
            $updatePhoneCmd = $this->messageFactory->newUpdateUserPhoneNumber($form->getUserGuid(), $form->getPhoneNumber(), $form->getPhoneType());
            $this->applicationBus->dispatch($updatePhoneCmd);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Save a user's address.
     *
     * @Rest\Post("/user/address/save")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\User\UpdateAddress", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param UpdateAddress                    $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function saveAddress(
        Request $request,
        UpdateAddress $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('accountProfileAddress', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // update the user address
            $updateAddrCmd = $this->messageFactory->newUpdateUserAddress($form->getUserGuid(), $form->getStreetAddress(), $form->getCity(), $form->getState(), $form->getPostalCode(), $form->getCountry(), $form->getType());
            $this->applicationBus->dispatch($updateAddrCmd);

            return new JsonResponse(array('message' => 'That was easy!'),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
