<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */

namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class AcceptTransitionListener.
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class AcceptTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $dataBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * AcceptTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $dataBus
     * @param MessageFactory      $messageFactory
     */
    public function __construct(MessageBusInterface $dataBus, MessageFactory $messageFactory)
    {
        $this->dataBus        = $dataBus;
        $this->messageFactory = $messageFactory;
    }


    /**
     * Transitions an invitee into a participant of the agreement.
     *
     * @param Event $event
     */
    public function acceptInvitation(Event $event)
    {
        // get workflow subject
        $invitee = $event->getSubject();

        // update 'accepted' col in database
        $acceptCmd = $this->messageFactory->newAcceptInvitation($invitee->getAgreementId()->value(), $invitee->getUserId()->value());
        $this->dataBus->dispatch($acceptCmd);

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
            'workflow.agreement_invitee.transition.accept' => 'acceptInvitation'
        ];
    }

}