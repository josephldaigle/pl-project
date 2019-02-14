<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/5/18
 */

namespace PapaLocal\Controller\Api;


use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\Core\Security\SecureLinkGenerator;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Data\Ewebify;
use PapaLocal\Entity\Exception\UsernameExistsException;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Form\CreateUserAccountForm;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\ValueObject\Form\ForgotPassword;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;


/**
 * Class SecurityController.
 */
class SecurityController extends FOSRestController
{
	use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
	private $iaMessageFactory;

    /**
     * @var MessageBusInterface
     */
	private $applicationBus;

    /**
     * @var LoggerInterface
     */
	private $logger;

    /**
     * SecurityController constructor.
     *
     * @param MessageFactory $iaMessageFactory
     * @param MessageBusInterface $applicationBus
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessageFactory $iaMessageFactory,
        MessageBusInterface $applicationBus,
        LoggerInterface $logger
    )
    {
        $this->iaMessageFactory = $iaMessageFactory;
        $this->applicationBus   = $applicationBus;
        $this->logger           = $logger;
    }

    /**
     * Register a new system user.
     *
     * @Rest\Post("/register")
     * @ParamConverter("form", class="PapaLocal\IdentityAccess\Form\CreateUserAccountForm", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param CreateUserAccountForm            $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     * @throws \Exception
     */
	public function registerUser(Request $request,
	                             CreateUserAccountForm $form,
	                             ConstraintViolationListInterface $validationErrors)
    {
        // create user account
        try {
            // validate CSRF token
            $this->validateFormToken('userRegistration', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            // send createUserAccount request
            $createUsrCmd = $this->iaMessageFactory->newCreateUserAccount($form);
            $this->applicationBus->dispatch($createUsrCmd);

            return new JsonResponse(array('message' => 'Your account has been created.'), JsonResponse::HTTP_OK);

        } catch (UsernameExistsException $userExistsException) {
            $this->logger->error(sprintf('The username %s is already in use.', $form->getUsername()),
                [$userExistsException->getFile(), $userExistsException->getLine()]);

            return new JsonResponse(array('message' => RestResponseMessage::USERNAME_IN_USE), JsonResponse::HTTP_BAD_REQUEST);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array($exception));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

	}

    /**
     * Generates an email, which allows the user to create a new password.
     *
     * @Rest\Post("/forgot-password")
     * @ParamConverter("form", class="PapaLocal\ValueObject\Form\ForgotPassword",
     *     converter="fos_rest.request_body", options={"validator"={"groups"={"create"}}})
     *
     * @param Request                          $request
     * @param ForgotPassword                   $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param SecureLinkGenerator              $secureLinkGenerator
     * @param \Swift_Mailer                    $mailer
     *
     * @return JsonResponse
     */
	public function forgotPassword(Request $request,
	                               ForgotPassword $form,
	                               ConstraintViolationListInterface $validationErrors,
	                               SecureLinkGenerator $secureLinkGenerator,
	                               \Swift_Mailer $mailer)
	{
        try {
            // validate CSRF token
            $this->validateFormToken('forgotPassword', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) { return $response; }

            // user is not logged in, so need to load from storage - why? to know who he is?
            // TODO: Replace with message to IdentityAccess
            $emailAddress = new EmailAddress($form->getUsername(), EmailAddressType::USERNAME());

            $findUserQry = $this->iaMessageFactory->newFindUserByUsername($emailAddress->getEmailAddress());
            $user = $this->applicationBus->dispatch($findUserQry);

            // create email link
            $link = $secureLinkGenerator->generateSecureLink($emailAddress, EmailSaltPurpose::PURPOSE_FORGOT_PASS(), 'reset_password', new \DateInterval('PT30M'));

            // create email message
			$message = (new \Swift_Message('Password reset link for your ' . Ewebify::APP_NAME . ' account'))
				->setFrom(Ewebify::ADMIN_EMAIL)
				->setTo($user->getUsername())
				->setBody(
					$this->renderView(
						'emails/resetPassword.html.twig',
						array('url' => $link)
					),
					'text/html'
				);

			if ( ! $mailer->send($message) > 0) {
				// email failed, user will not be able to reset password
				$this->logger->error(sprintf('Failed sending password change success email to %s',
					$form->getUsername()));

				return new JsonResponse(array('message' => RestResponseMessage::PASSWORD_RESET_EMAIL_FAILED),
					JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
			}

			return new JsonResponse(array('message' => RestResponseMessage::PASSWORD_RESET_EMAIL_SUCCESS),
				JsonResponse::HTTP_OK);

		} catch (UserNotFoundException $une) {
			$this->logger->warning($une->getMessage());

			return new JsonResponse(array('message' => 'That username cannot be found.'),
				JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		} catch (\Exception $exception) {
			$this->logger->warning($exception->getMessage(), array($exception));

			return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
				JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}