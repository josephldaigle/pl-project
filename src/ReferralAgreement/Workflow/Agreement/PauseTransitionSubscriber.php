<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/2/18
 * Time: 8:26 PM
 */

namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class PauseTransitionSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class PauseTransitionSubscriber implements EventSubscriberInterface
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
    public function pauseAgreement(Event $event)
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
            'workflow.referral_agreement.transition.pause' => 'pauseAgreement'
        ];
    }


}