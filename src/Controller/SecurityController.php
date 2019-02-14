<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 8:45 PM
 */


namespace PapaLocal\Controller;


use PapaLocal\Core\Security\EmailSaltPurpose;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\Person;
use PapaLocal\Notification\Account\ChangePassword;
use PapaLocal\Entity\User;
use PapaLocal\Response\RestResponseMessage;
use PapaLocal\Core\Security\Cryptographer;
use PapaLocal\Notification\Notifier;
use PapaLocal\IdentityAccess\Service\UserAccountManager;
use PapaLocal\ValueObject\Form\RegisterUser;
use PapaLocal\ValueObject\Form\ResetPassword as ResetPasswordForm;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * SecurityController.
 *
 * Provide login, registration or password reset type actions.
 *
 * @package PapaLocal\Controller
 */
class SecurityController extends AbstractController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SecurityController constructor.
     *
     * @param MessageBusInterface $applicationBus
     * @param MessageFactory      $iaMessageFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(
        MessageBusInterface $applicationBus,
        MessageFactory $iaMessageFactory,
        LoggerInterface $logger
    )
    {
        $this->applicationBus   = $applicationBus;
        $this->iaMessageFactory = $iaMessageFactory;
        $this->logger           = $logger;
    }

    /**
     * Registers a referral agreement invitee as a new user.
     * This happens when the user responds by clicking the link emailed to them, and
     * helps ensure we can track participation.
     *
     * @Route("/register-invitee", name="register_invitee", methods={"POST"})
     *
     * @param Request                      $request
     * @param SerializerInterface          $serializer
     * @param ValidatorInterface           $validator
     * @param UserAccountManager           $userAccountManager
     * @param RepositoryRegistry           $repositoryRegistry
     * @param UserPasswordEncoderInterface $encoder
     * @param Notifier                     $notifier
     *
     * @return RedirectResponse|Response
     */
    public function registerReferralAgreementInvitee(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserAccountManager $userAccountManager,
        RepositoryRegistry $repositoryRegistry,
        UserPasswordEncoderInterface $encoder,
        Notifier $notifier
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('referralAgreementInviteeRegistration', $request);

            // validate user input
            $registerUserForm = $serializer->denormalize($request->request->all(), RegisterUser::class, 'array');
            $errors           = $validator->validate($registerUserForm);

            if ($errors->count() > 0) {
                // has errors
                return $this->render('pages/acceptReferralAgreementInvitation.html.twig', array(
                    'validationErrors'      => $errors,
                    'agreementInvitationId' => $request->request->get('agreementInvitationId'),
                    'form'                  => $registerUserForm,
                ));
            }

            $invitation = $repositoryRegistry->get(InviteeRepository::class)->findById($request->request->get('agreementInvitationId'));

            $newUser = null;
            if (is_null($invitation)) {
                // no invitation found, user will only have welcome item in feed
                // create all new user
                $user = (new User())
                    ->setPassword($encoder->encodePassword(new User(), $registerUserForm->getPassword()))
                    ->setUsername($registerUserForm->getUsername());

                $person = (new Person())
                    ->setFirstName($registerUserForm->getFirstName())
                    ->setLastName($registerUserForm->getLastName());

                $newUser = $repositoryRegistry->get(UserRepository::class)->createUserAccount($user, $person);
            } else {
                // register the person from the invitation as a user
                $registerUserForm->setPassword($encoder->encodePassword(new User(), $registerUserForm->getPassword()));
                $newUser = $userAccountManager->registerReferralAgreementInvitee($invitation, $registerUserForm);
            }

            // send user a welcome notification
            $welcomeNotification = new \PapaLocal\Notification\Account\RegisterUser($registerUserForm->getUsername(),
                array());
            $notifier->sendUserNotification($newUser->getGuid(), $welcomeNotification);

            $this->addFlash('success', 'You\'ve successfully registered. You can now login.');

            return new RedirectResponse('/login');

        } catch (\Exception $exception) {
            $this->logger->error('An unexpected exception occurred.', array($exception));

            return $this->render('pages/acceptReferralAgreementInvitation.html.twig',
                array('errors'                => RestResponseMessage::INTERNAL_SERVER_ERROR,
                      'agreementInvitationId' => $request->request->get('agreementInvitationId'),
                ));
        }
    }


    /**
     * Display the login page to the user.
     *
     * @Route("/login/{emailAddress}", defaults={"emailAddress"=""}, name="login_get", methods={"GET"})
     *
     * @param string                        $emailAddress will be present if the registrant uses an invitation link
     * @param AuthorizationCheckerInterface $authChecker
     *
     * @return RedirectResponse|Response
     */
    public function loginPage(
        $emailAddress,
        AuthorizationCheckerInterface $authChecker
    )
    {
        // bypass login form for authenticated users (user manually attempts to nav to /login, after authenticating)
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('feed');
        }

        // decode url params
        $emailAddress = urldecode($emailAddress);

        // launch register form if email present in url params (user used an email link to register)
        if ( ! empty($emailAddress)) {
            return $this->render('pages/login.html.twig', array(
                'emailAddress'    => urldecode($emailAddress),
                'footerResources' => array('<script type="text/javascript">$(\'#register\').modal();</script>'),
            ));
        }

        return $this->render('pages/login.html.twig', array());

    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     *
     * @param AuthorizationCheckerInterface $authChecker
     * @param AuthenticationUtils           $authUtils
     *
     * @return RedirectResponse|Response
     */
    public function loginAction(
        AuthorizationCheckerInterface $authChecker,
        AuthenticationUtils $authUtils
    )
    {
        try {
            // bypass login form for authenticated users (user manually attempted to nav to /login, after authenticating)
            if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                return $this->redirectToRoute('feed');
            }

            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();

            //log the error
            if ($error) {
                $this->logger->notice(sprintf('User %s failed login due to %s',
                    $authUtils->getLastUsername(),
                    strtolower($error->getMessage())
                ), [$error]);

                // add flash message
                $this->addFlash('danger', 'Sorry, that username and password didn\'t work.');
            }

            // last username entered by the user
            $lastUsername = $authUtils->getLastUsername();

            return $this->render('pages/login.html.twig', array(
                'lastUsername' => $lastUsername,
            ));
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array($exception));
        }
    }


    /**
     * Displays the reset-password form to a user, after the user has clicked an email link.
     *
     * @Route("/reset-password/{emailAddress}/{key}", name="reset_password", methods={"GET"})
     *
     * @param Request            $request
     * @param RepositoryRegistry $repositoryRegistry
     * @param Cryptographer      $cryptographer
     * @param MessageFactory     $identityMsgFactory
     * @param                    $emailAddress
     * @param                    $key
     *
     * @return RedirectResponse|Response
     */
    public function showResetPasswordForm(
        Request $request,
        RepositoryRegistry $repositoryRegistry,
        Cryptographer $cryptographer,
        MessageFactory $identityMsgFactory,
        $emailAddress,
        $key
    )
    {
        try {

            // try to load user
            $findUserCmd = $identityMsgFactory->newFindUserByUsername($emailAddress);
            $user        = $this->applicationBus->dispatch($findUserCmd);

            // try to load email key
            $emailSaltRepository = $repositoryRegistry->get(EmailSaltRepository::class);
            $resetPassSalt       = $emailSaltRepository->findBy(array(
                'purpose'      => EmailSaltPurpose::PURPOSE_FORGOT_PASS()->getValue(),
                'emailAddress' => $emailAddress,
            ));

            // delete the email salt no matter what (destroys link)
            $emailSaltRepository->deleteSalt($resetPassSalt->getId());

            // user has a forgot password salt
            if ($resetPassSalt->isExpired()) {
                $this->logger->error(sprintf('User [%s]\'s password reset key is expired.',
                    $user->getUsername()));

                // rediredect to login
                $this->addFlash('danger', 'It looks like your link expired. No sweat, just request another one!');

                return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);
            }

            // verify key
            if (false === $cryptographer->verify($resetPassSalt->getHash(), $key)) {
                $this->logger->error(sprintf('User [%s]\'s password reset key is invalid: %s.',
                    $user->getUsername(), $resetPassSalt->getSalt()), array('Failed decryption.'));

                // rediredect to login
                $this->addFlash('danger', 'We couldn\'t validate your link. No sweat, just request another one!');

                return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);
            }

            // user request is verified, show reset password form
            return $this->render('pages/passwordReset.html.twig', array('username' => $user->getUsername()));

        } catch (UserNotFoundException $userNotFoundException) {
            // user not found
            $this->logger->error(sprintf('An unidentified attempt to access reset-password has been made by %s: ',
                $request->getClientIp(), 'The user could not be found.'));

            $this->addFlash('danger', 'We couldn\'t verify your link.');

            return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            // user not found
            $this->logger->warning(sprintf('An attempt to access reset-password has been made by %s. %s',
                $request->getClientIp(), $exception->getMessage()), array($exception));

            $this->addFlash('danger', 'We couldn\'t verfiy your link. No sweat, you can create another one.');

            return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * Process a password-reset request (user has submitted new password).
     *
     * @Route("/process-password-reset/", name="process_password_reset", methods={"POST"})
     *
     * @param Request             $request
     * @param SerializerInterface $serializer
     * @param Notifier            $notifier
     * @param ValidatorInterface  $validator
     *
     * @return RedirectResponse|Response
     */
    public function processResetPasswordRequest(
        Request $request,
        SerializerInterface $serializer,
        Notifier $notifier,
        ValidatorInterface $validator
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('resetPassword', $request);

            // validate user input
            $resetPassword = $serializer->denormalize($request->request->all(), ResetPasswordForm::class, 'array');
            $errors        = $validator->validate($resetPassword, null, array('authenticated_change'));

            if ($errors->count() > 0) {
                // has errors
                return $this->render('pages/passwordReset.html.twig',
                    array('errors' => $errors, 'username' => $request->request->get('username')));
            }

            // load user from db
            $findUserCmd = $this->iaMessageFactory->newFindUserByUsername($request->request->get('username'));
            $user        = $this->applicationBus->dispatch($findUserCmd);

            $updatePassCmd = $this->iaMessageFactory->newUpdatePassword($user->getGuid(),
                $request->request->get('password'));
            $this->applicationBus->dispatch($updatePassCmd);

            // reset successful
            $this->addFlash('success',
                'Your password has successfully been reset. You can login using your new password.');

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), array($exception));
            $this->addFlash('danger', 'We were not able to reset your password. You can try again, or contact us.');

            return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);
        }

        // notify user
        try {
            $notifier->sendUserNotification($user->getGuid(),
                new ChangePassword($request->request->get('username'), array()));

            return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);

        } catch (\Exception $exception) {

            $this->logger->error(sprintf('An erro occured while sending an email to user %s: %s', $user->getUsername(), $exception->getMessage()), array($exception));

            return new RedirectResponse($request->getBaseUrl().'/login', Response::HTTP_SEE_OTHER);

        }

    }
}