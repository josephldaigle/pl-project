<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/31/19
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class ActivateTransitionSubscriber.
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class ActivateTransitionSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * PauseTransitionSubscriber constructor.
     *
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $mysqlMsgFactory
     */
    public function __construct(MessageBusInterface $mysqlBus,
                                MessageFactory $mysqlMsgFactory)
    {
        $this->mysqlBus        = $mysqlBus;
        $this->mysqlMsgFactory = $mysqlMsgFactory;
    }

    /**
     * @param Event $event
     */
    public function activateAgreement(Event $event)
    {
        // update agreement status
        $agreement = $event->getSubject();
        $status = $agreement->getStatusHistory()->getCurrentStatus();


        $command = $this->mysqlMsgFactory->newUpdateAgreementStatus($status);
        $this->mysqlBus->dispatch($command);

        return;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_agreement.transition.activate' => 'activateAgreement'
        ];
    }

}