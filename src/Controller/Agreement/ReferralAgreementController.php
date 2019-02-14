<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/11/18
 */

namespace PapaLocal\Controller\Agreement;


use PapaLocal\ReferralAgreement\Data\ReferralAgreementHydrator;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\Core\Security\EmailSaltRepository;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\Response\RestResponseMessage;
use Psr\Log\LoggerInterface;
use PapaLocal\Core\Security\Cryptographer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * ReferralAgreementController.
 *
 * @package PapaLocal\Controller\Agreement
 */
class ReferralAgreementController extends AbstractController
{
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
        $this->logger         = $logger;
    }


    /**
     * Fetches a participant's agreements.
     *
     * @Route("/agreement/participant/agreements/all", name="load_participant_agreements", methods={"GET"})
     *
     * @param Request                     $request
     * @param TokenStorageInterface       $tokenStorage
     * @return Response
     */
    public function fetchParticipantAgreements(Request $request,
                                               TokenStorageInterface $tokenStorage)
    {
        try {
            $user = $tokenStorage->getToken()->getUser();

            // query module for data
            $agmtQuery = $this->messageFactory->newLoadParticipantAgreements($user->getGuid());
            $agreementList = $this->applicationBus->dispatch($agmtQuery);

            $formData = [];

            if ($agreementList->count() < 1) {
                // no agreements found
                // render the template part
                return $this->render('fragments/formFields/addReferral/agreementRecipientSelect.html.twig',
                    array('agreements' => ['-1' => 'None Available'])
                );
            }

            // convert agreements to map
            foreach ($agreementList as $agreement) {
                if ($agreement->getCurrentPlace() === Status::ACTIVE()->getValue()) {
                    $formData[$agreement->getGuid()->value()] = $agreement->getTitle();
                }
            }

            // render the template part
            return $this->render('fragments/formFields/addReferral/agreementRecipientSelect.html.twig',
                array('agreements' => $formData)
            );

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));

            // return error response
            return new Response(RestResponseMessage::INTERNAL_SERVER_ERROR, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Allows a referral agreement invitee to accept the invitation.
     *
     * @Route("agreement/referral/join/{emailAddress}/{saltId}/{key}/{agreementId}", name="accept_referral_agreement_invitation", methods={"GET"})
     *
     * @param Request                     $request
     * @param EmailSaltRepository         $emailSaltRepository
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param ReferralAgreementHydrator   $referralAgreementHydrator
     * @param Cryptographer               $cryptographer
     * @param LoggerInterface             $logger
     * @param                             $emailAddress
     * @param                             $agreementId
     * @param                             $saltId
     * @param                             $key
     *
     * @return RedirectResponse
     */
    public function acceptAgreementInvitation(Request $request,
                                          EmailSaltRepository $emailSaltRepository,
                                          ReferralAgreementRepository $referralAgreementRepository,
                                          ReferralAgreementHydrator $referralAgreementHydrator,
                                          Cryptographer $cryptographer,
                                          LoggerInterface $logger,
                                          $emailAddress,
                                          $agreementId,
                                          $saltId,
                                          $key)
    {
        // decode request params
        $emailAddress = urldecode($emailAddress);
        $agreementId = urldecode($agreementId);
        $saltId = urldecode($saltId);
        $key = urldecode($key);

        try {

            // validate email hash
	        if (!is_null($storedSalt = $emailSaltRepository->loadSaltById($saltId))) {
		        if (! $cryptographer->verify($key, $storedSalt->getSalt())) {
			        $logger->error(sprintf('Unable to validate email key for personId [%s].', $storedSalt->getPersonId()), array('storedSalt' => $storedSalt));
			        $this->addFlash('danger', 'We couldn\'t validate your link. Please create an account to login.');
			        return new RedirectResponse($request->getBaseUrl() . '/login', Response::HTTP_SEE_OTHER);
		        }

		        // salt verified
		        // load invitee obj from database
		        $invitee = $referralAgreementRepository->loadInvitee($agreementId, $emailAddress);

		        if (is_null($invitee)) {
		            // invitee not found
                    $logger->error('Agreement invitation not found.', array('agreementId' => $agreementId, 'storedSalt' => $storedSalt, 'emailAddress' => $emailAddress));
                    return new RedirectResponse($request->getBaseUrl() . '/login', Response::HTTP_SEE_OTHER);
                }

                if (! $invitee->isUser()) {
                    // render registration form with personId for non-user
                    $logger->debug('User clicked referral agreement invitation link.');
                    return $this->render('pages/acceptReferralAgreementInvitation.html.twig', array(
                        'agreementInvitationId' => $invitee->getId(),
                        'form' => array(
                            'firstName' => $invitee->getPerson()->getFirstName(),
                            'lastName' => $invitee->getPerson()->getLastName(),
                            'username' => $emailAddress,
                            'phoneNumber' => $invitee->getPhoneNumber(),
                        )
                    ));
                }

                // render login page for user
                $this->addFlash('success', 'Login to see the invitation in your feed!');
                return new RedirectResponse($request->getBaseUrl() . '/login', Response::HTTP_SEE_OTHER);

	        } else {
	        	$logger->error(sprintf('Unable to locate salt for %s, accepting invitation for agreement %s.', $emailAddress, $agreementId));

	        	$this->addFlash('danger', 'We couldn\'t validate your link. Please create an account to login.');

		        return new RedirectResponse($request->getBaseUrl() . '/login', Response::HTTP_SEE_OTHER);
	        }

        } catch (\Exception $exception) {
            $logger->error('An unexpected exception occurred.', array($exception));
            $this->addFlash('danger', RestResponseMessage::INTERNAL_SERVER_ERROR);
            return new RedirectResponse($request->getBaseUrl() . '/login', Response::HTTP_SEE_OTHER);
        }

    }
}