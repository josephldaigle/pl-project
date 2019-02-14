<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Referral\Data\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredFinalizedSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class TransitionAdminReviewSubscriber implements EventSubscriberInterface
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
     * TransitionAdminReviewSubscriber constructor.
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
     * @throws \Exception
     */
    public function adminReview(Event $event)
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
            'workflow.referral_delivering.transition.admin_review' => array('adminReview')
        ];
    }
}