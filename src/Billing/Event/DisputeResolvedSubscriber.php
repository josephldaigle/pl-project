<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/4/18
 * Time: 11:12 AM
 */

namespace PapaLocal\Billing\Event;


use PapaLocal\Billing\Message\MessageFactory;
use PapaLocal\Referral\Event\DisputeResolvedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class DisputeResolvedSubscriber
 *
 * @package PapaLocal\Billing\Event
 */
class DisputeResolvedSubscriber implements EventSubscriberInterface
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
     * DisputeResolvedSubscriber constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $appBus
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $appBus)
    {
        $this->messageFactory = $messageFactory;
        $this->appBus = $appBus;
    }

    /**
     * @param DisputeResolvedEvent $event
     */
    public function processTransaction(DisputeResolvedEvent $event)
    {
        if($event->getStatus() == 'approved') {
            $refundAccountCmd = $this->messageFactory->newRefundAccount($event->getAgreementGuid(), $event->getReferralGuid());
            $this->appBus->dispatch($refundAccountCmd);
        }
        return;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DisputeResolvedEvent::class => array('processTransaction')
        ];
    }
}