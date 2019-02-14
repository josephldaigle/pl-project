<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Exception\InvalidStateException;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class TransitionAcquireSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class TransitionAcquireSubscriber implements EventSubscriberInterface
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
     * TransitionAcquireSubscriber constructor.
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
    public function acquireReferral(Event $event)
    {
        if ($event->getSubject()->getRecipient() instanceof AgreementRecipient) {
            $referral = $event->getSubject();

            $saveReferralCmd = $this->messageFactory->newSaveReferral($referral, 'acquired');
            $this->mysqlBus->dispatch($saveReferralCmd);
        }

        if ($event->getSubject()->getRecipient() instanceof ContactRecipient) {
            $referral = $event->getSubject();

            $UpdateReferralCmd = $this->messageFactory->newUpdateReferral($referral, 'acquired');
            $this->mysqlBus->dispatch($UpdateReferralCmd);
        }
        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.transition.acquire' => array('acquireReferral')
        ];
    }

}