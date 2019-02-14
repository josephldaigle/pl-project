<?php
/**
 * Created by PhpStorm.
 * Date: 9/11/18
 * Time: 1:08 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class GuardAcquireSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class GuardAcquireSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     *
     * Referral Agreement
     */
    private $raMessageFactory;

    /**
     * @var \PapaLocal\IdentityAccess\Message\MessageFactory
     *
     * Identity Agreement
     */
    private $iaMessageFactory;

    /**
     * GuardAcquireSubscriber constructor.
     * @param MessageBusInterface $appBus
     * @param MessageFactory $raMessageFactory
     * @param \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory
     */
    public function __construct(MessageBusInterface $appBus, MessageFactory $raMessageFactory, \PapaLocal\IdentityAccess\Message\MessageFactory $iaMessageFactory)
    {
        $this->appBus = $appBus;
        $this->raMessageFactory = $raMessageFactory;
        $this->iaMessageFactory = $iaMessageFactory;
    }

    /**
     * @param GuardEvent $event
     */
    public function confirmCanAcquireReferral(GuardEvent $event)
    {
        if ($event->getSubject()->getRecipient() instanceof AgreementRecipient) {
            $query = $this->raMessageFactory->newFindAgreementByGuid($event->getSubject()->getRecipient()->getGuid());
            $agreement = $this->appBus->dispatch($query);

            if ($agreement->getStatusHistory()->getCurrentStatus()->getStatus() == 'Inactive') {
                $event->addTransitionBlocker(
                    new TransitionBlocker('Inactive agreement.', ReferralGuardBlockCode::INACTIVE_AGREEMENT, array())
                );
            }
        }

        if ($event->getSubject()->getRecipient() instanceof ContactRecipient) {
            $query = $this->iaMessageFactory->newFindUserByUsername($event->getSubject()->getRecipient()->getEmailAddress()->getEmailAddress());
            try {
                $this->appBus->dispatch($query);
            } catch (UserNotFoundException $e) {
                $event->addTransitionBlocker(
                    new TransitionBlocker('User not found.', ReferralGuardBlockCode::USER_NOT_FOUND, array())
                );
            }
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.guard.acquire' => array('confirmCanAcquireReferral')
        ];
    }
}