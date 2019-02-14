<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Exception\InvalidStateException;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class TransitionCreateSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class TransitionCreateSubscriber implements EventSubscriberInterface
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
     * TransitionCreateSubscriber constructor.
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
    public function saveContactReferral(Event $event)
    {
        if ($event->getSubject()->getRecipient() instanceof ContactRecipient) {
            $referral = $event->getSubject();

            $saveReferralCmd = $this->messageFactory->newSaveReferral($referral, 'created');
            $this->mysqlBus->dispatch($saveReferralCmd);
        }
        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.transition.create' => array('saveContactReferral')
        ];
    }
}