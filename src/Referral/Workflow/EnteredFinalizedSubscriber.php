<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Notification\Notifier;
use PapaLocal\Referral\Event\DisputeResolvedEvent;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredFinalizedSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class EnteredFinalizedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailMessageBuilder
     */
    private $emailMessageBuilder;

    /**
     * @var Emailer
     */
    private $emailer;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     *
     * Referral Agreement
     */
    private $raMessageFactory;

    /**
     * @var \PapaLocal\IdentityAccess\Message\MessageFactory
     *
     * Identity Access
     */
    private $iaMessageFactory;

    /**
     * EnteredFinalizedSubscriber constructor.
     * @param EmailMessageBuilder $emailMessageBuilder
     * @param Emailer $emailer
     * @param EventDispatcherInterface $eventDispatcher
     * @param NotificationFactory $notificationFactory
     * @param Notifier $notifier
     * @param MessageBusInterface $appBus
     * @param MessageFactory $raMessageFactory
     * @param \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory
     */
    public function __construct(EmailMessageBuilder $emailMessageBuilder, Emailer $emailer, EventDispatcherInterface $eventDispatcher, NotificationFactory $notificationFactory, Notifier $notifier, MessageBusInterface $appBus, MessageFactory $raMessageFactory, \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory)
    {
        $this->emailMessageBuilder = $emailMessageBuilder;
        $this->emailer = $emailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationFactory = $notificationFactory;
        $this->notifier = $notifier;
        $this->appBus = $appBus;
        $this->raMessageFactory = $raMessageFactory;
        $this->iaMessageFactory = $iaMessageFactory;
    }

    /**
     * @param Event $event
     * @throws \Exception
     */
    public function finalizeReferral(Event $event)
    {
        if ($event->getSubject()->getRecipient() instanceof AgreementRecipient) {

            if ($event->getSubject()->getRating()->getScore() < 3) {
                $this->dispatchDisputeResolved(
                    $event->getSubject()->getGuid(),
                    $event->getSubject()->getRecipient()->getGuid(),
                    $event->getSubject()->getRating()->getResolution()
                );
            }

            $agreementQuery = $this->raMessageFactory->newFindAgreementByGuid($event->getSubject()->getRecipient()->getGuid());
            $agreement = $this->appBus->dispatch($agreementQuery);

            $recipientQuery = $this->iaMessageFactory->newFindUserByGuid($agreement->getOwnerGuid());

        } else {
            $recipientQuery = $this->iaMessageFactory->newFindUserByGuid($event->getSubject()->getRecipient()->getContactGuid());
        }

        $recipient = $this->appBus->dispatch($recipientQuery);

        $providerQuery = $this->iaMessageFactory->newFindUserByGuid($event->getSubject()->getProviderUserGuid());
        $provider = $this->appBus->dispatch($providerQuery);

        $recipientNotification = $this->notificationFactory->newReferralFinalization($recipient, $provider);
        $this->notifier->sendUserNotification($recipient->getGuid(), $recipientNotification);

        $providerNotification = $this->notificationFactory->newReferralFinalizationConfirmation($recipient, $provider);
        $this->notifier->sendUserNotification($event->getSubject()->getProviderUserGuid(), $providerNotification);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.entered.finalized' => array('finalizeReferral')
        ];
    }

    /**
     * @param GuidInterface $referralGuid
     * @param GuidInterface $AgreementGuid
     * @param string $status
     */
    protected function dispatchDisputeResolved(GuidInterface $referralGuid, GuidInterface $AgreementGuid, string $status)
    {
        $finalizedEvent = new DisputeResolvedEvent($referralGuid, $AgreementGuid, $status);
        $this->eventDispatcher->dispatch(DisputeResolvedEvent::class, $finalizedEvent);
        return;
    }
}