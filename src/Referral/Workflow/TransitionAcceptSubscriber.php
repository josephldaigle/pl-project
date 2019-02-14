<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Exception\InvalidStateException;
use PapaLocal\Referral\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class transitionAcceptSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class TransitionAcceptSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * TransitionAcceptSubscriber constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $mysqlBus
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $mysqlBus)
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus = $mysqlBus;
    }

    /**
     * @param Event $event
     * @throws InvalidStateException
     */
    public function updateReferralRate(Event $event)
    {
        $referral = $event->getSubject();

        $UpdateReferralCmd = $this->messageFactory->newUpdateReferral($referral, 'finalized');
        $this->mysqlBus->dispatch($UpdateReferralCmd);

        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.transition.accept' => array('updateReferralRate')
        ];
    }
}