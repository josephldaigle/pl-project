<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 10/4/18
 * Time: 11:12 AM
 */

namespace PapaLocal\Billing\Event;


use PapaLocal\Billing\Message\MessageFactory;
use PapaLocal\Referral\Event\ReferralCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class ReferralCreatedSubscriber
 *
 * @package PapaLocal\Billing\Event
 */
class ReferralCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * ReferralCreatedSubscriber constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $appBus
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $appBus)
    {
        $this->messageFactory = $messageFactory;
        $this->appBus = $appBus;
    }

    /**
     * @param ReferralCreatedEvent $event
     */
    public function chargeForReferral(ReferralCreatedEvent $event)
    {
        $chargeAccountCmd = $this->messageFactory->newChargeAccount($event->getAgreementGuid(), $event->getReferralGuid());
        $this->appBus->dispatch($chargeAccountCmd);
        return;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ReferralCreatedEvent::class => array('chargeForReferral')
        ];
    }
}