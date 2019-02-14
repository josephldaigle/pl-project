<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 3:02 PM
 */

namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class DeclineTransitionSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class DeclineTransitionSubscriber implements EventSubscriberInterface
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
     * DeclineTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $messageFactory
     */
    public function __construct(MessageBusInterface $mysqlBus, MessageFactory $messageFactory)
    {
        $this->mysqlBus = $mysqlBus;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param Event $event
     */
    public function declineInvitation(Event $event)
    {
        $invitee = $event->getSubject();


        $declineCmd = $this->messageFactory->newDeclineInvitation($invitee->getGuid());
        $this->mysqlBus->dispatch($declineCmd);

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
            'workflow.agreement_invitee.transition.decline' => 'declineInvitation'
        ];
    }

}