<?php
/**
 * Created by Joseph Daigle.
 * Date: 2/7/19
 * Time: 8:35 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * RemoveTransitionSubscriber.
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class RemoveTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * RemoveTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $messageFactory
     */
    public function __construct(MessageBusInterface $mysqlBus, MessageFactory $messageFactory)
    {
        $this->mysqlBus       = $mysqlBus;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param Event $event
     *
     * @return Event
     */
    public function removeInvitee(Event $event)
    {
        $invitee = $event->getSubject();

        $removeCmd = $this->messageFactory->newRemoveInvitee($invitee->getGuid());
        $this->mysqlBus->dispatch($removeCmd);

        return $event;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.transition.remove' => 'removeInvitee'
        ];
    }

}