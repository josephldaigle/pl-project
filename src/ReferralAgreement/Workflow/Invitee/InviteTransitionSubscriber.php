<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 2:23 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\Core\Notification\EmailerInterface;
use PapaLocal\Data\Ewebify;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\InviteeService;
use PapaLocal\ReferralAgreement\Notification\NotificationFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class InviteTransitionSubscriber
 *
 * This class is responsible for sending notifications to agreement invitees.
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class InviteTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailerInterface
     */
    private $emailer;

    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * InviteTransitionSubscriber constructor.
     *
     * @param EmailerInterface            $emailer
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param InviteeService              $inviteeService
     * @param InviteeRepository           $inviteeRepository
     * @param MessageFactory              $iaMessageFactory
     * @param MessageBusInterface         $appBus
     * @param NotificationFactory         $notificationFactory
     * @param Notifier                    $notifier
     */
    public function __construct(
        EmailerInterface $emailer,
        ReferralAgreementRepository $referralAgreementRepository,
        InviteeService $inviteeService,
        InviteeRepository $inviteeRepository,
        MessageFactory $iaMessageFactory,
        MessageBusInterface $appBus,
        NotificationFactory $notificationFactory,
        Notifier $notifier
    )
    {
        $this->emailer                     = $emailer;
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->inviteeService              = $inviteeService;
        $this->inviteeRepository           = $inviteeRepository;
        $this->iaMessageFactory            = $iaMessageFactory;
        $this->appBus                      = $appBus;
        $this->notificationFactory         = $notificationFactory;
        $this->notifier                    = $notifier;
    }


    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function inviteTransition(Event $event)
    {
        // load agreement
        $invitee           = $event->getSubject();
        $referralAgreement = $this->referralAgreementRepository->findByGuid($invitee->getAgreementId());

        // load owner
        $ownerQuery     = $this->iaMessageFactory->newFindUserByGuid($referralAgreement->getOwnerGuid());
        $agreementOwner = $this->appBus->dispatch($ownerQuery);

        if (! $invitee->isUser()) {
            // invitee is not user, so send an email invitation
            // user's will automatically see the invitation in their feed
            $emailMessage = $this->emailer->getMessageBuilder()
                                          ->sendTo($invitee->getEmailAddress()->getEmailAddress())
                                          ->subject(sprintf('%s %s has invited you to sell them referrals.',
                                              $agreementOwner->getFirstName(), $agreementOwner->getLastName()))
                                          ->usingTwigTemplate('emails/agreement/referralAgreementInvitation.html.twig',
                                              array(
                                                  'owner'   => $agreementOwner,
                                                  'invitee' => $invitee,
                                                  'url'     => Ewebify::WEB_ADDRESS,
                                              ))
                                          ->build();

            $this->emailer->send($emailMessage);
        }

        // mark invitation as sent
        $this->inviteeService->markInvitationAsSent($invitee->getGuid());

        return;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.transition.invite' => 'inviteTransition',
        ];
    }

}