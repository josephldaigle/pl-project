<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Data\Ewebify;
use PapaLocal\Feed\Enum\FeedItemType;
use PapaLocal\Notification\Notifier;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;
use PapaLocal\Referral\Event\ReferralDisputedEvent;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredDisputedSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class EnteredDisputedSubscriber implements EventSubscriberInterface
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

    private $serializer;

    /**
     * EnteredDisputedSubscriber constructor.
     * @param EmailMessageBuilder $emailMessageBuilder
     * @param Emailer $emailer
     * @param EventDispatcherInterface $eventDispatcher
     * @param NotificationFactory $notificationFactory
     * @param Notifier $notifier
     * @param MessageBusInterface $appBus
     * @param MessageFactory $raMessageFactory
     * @param \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory
     * @param $serializer
     */
    public function __construct(EmailMessageBuilder $emailMessageBuilder, Emailer $emailer, EventDispatcherInterface $eventDispatcher, NotificationFactory $notificationFactory, Notifier $notifier, MessageBusInterface $appBus, MessageFactory $raMessageFactory, \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory, $serializer)
    {
        $this->emailMessageBuilder = $emailMessageBuilder;
        $this->emailer = $emailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationFactory = $notificationFactory;
        $this->notifier = $notifier;
        $this->appBus = $appBus;
        $this->raMessageFactory = $raMessageFactory;
        $this->iaMessageFactory = $iaMessageFactory;
        $this->serializer = $serializer;
    }

    /**
     * @param Event $event
     * @throws \Exception
     */
    public function disputeReferral(Event $event)
    {
        if ($event->getSubject()->getRecipient() instanceof AgreementRecipient) {
            $agreementQuery = $this->raMessageFactory->newFindAgreementByGuid($event->getSubject()->getRecipient()->getGuid());
            $agreement = $this->appBus->dispatch($agreementQuery);

            $recipientQuery = $this->iaMessageFactory->newFindUserByGuid($agreement->getOwnerGuid());

        } else {
            $recipientQuery = $this->iaMessageFactory->newFindUserByGuid($event->getSubject()->getRecipient()->getContactGuid());
        }

        $recipient = $this->appBus->dispatch($recipientQuery);

        $providerQuery = $this->iaMessageFactory->newFindUserByGuid($event->getSubject()->getProviderUserGuid());
        $provider = $this->appBus->dispatch($providerQuery);

        $recipientNotification = $this->notificationFactory->newReferralDispute($recipient, $provider);
        $this->notifier->sendUserNotification($recipient->getGuid(), $recipientNotification);

        $providerNotification = $this->notificationFactory->newReferralDisputeConfirmation($recipient, $provider);
        $this->notifier->sendUserNotification($event->getSubject()->getProviderUserGuid(), $providerNotification);

        $sysAdminNotification = $this->notificationFactory->newReferralDisputeNotice(
            $this->serializer->denormalize(
                array(
                    'guid' => array('value' => $event->getSubject()->getGuid()->value()),
                    'type' => array('value' => FeedItemType::REFERRAL()->getValue())
                ), AssociateFeedItem::class, 'array'
            )
        );

        $this->notifier->sendUserNotification(
            // sysadmin Guid.
            $this->serializer->denormalize(
                array('value' => '20671da2-82c6-4b30-8140-b7146cc8033b'),
                Guid::class, 'array'
            ),
            $sysAdminNotification
        );

        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.entered.disputed' => array('disputeReferral')
        ];
    }
}