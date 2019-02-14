<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 9/11/18
 * Time: 1:08 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class GuardCreateSubscriber
 *
 *
 * @package PapaLocal\Referral\Workflow
 */
class GuardCreateSubscriber implements EventSubscriberInterface
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
     * GuardCreateSubscriber constructor.
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
    public function confirmCanCreateReferral(GuardEvent $event)
    {

        if ($event->getSubject()->getRecipient() instanceof AgreementRecipient) {
            $query = $this->raMessageFactory->newFindAgreementByGuid($event->getSubject()->getRecipient()->getGuid());
            $agreement = $this->appBus->dispatch($query);

            // block if agreement is inactive
            if ($agreement->getStatusHistory()->getCurrentStatus()->getStatus() == Status::INACTIVE()) {
                $event->addTransitionBlocker(
                    new TransitionBlocker('Inactive agreement.', ReferralGuardBlockCode::INACTIVE_AGREEMENT, array())
                );
            }

            // block if agreement quota is reached or will be exceeded.
            if ($agreement->getReferralCount() >= $agreement->getQuantity()) {
                $event->addTransitionBlocker(
                    new TransitionBlocker('Agreement quota reached.', ReferralGuardBlockCode::AGREEMENT_QUOTA, array())
                );
            }
        }

        if ($event->getSubject()->getRecipient() instanceof ContactRecipient) {
            $query = $this->iaMessageFactory->newFindUserByUsername($event->getSubject()->getRecipient()->getEmailAddress()->getEmailAddress());
            try {
                $this->appBus->dispatch($query);

                // User found - Should not transition
                $event->addTransitionBlocker(
                    new TransitionBlocker('Contact is user.', ReferralGuardBlockCode::CONTACT_IS_USER, array())
                );
            } catch (UserNotFoundException $e) {
                // Contact is not a user - can transition
                return;
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
            'workflow.referral_delivering.guard.create' => array('confirmCanCreateReferral')
        ];
    }
}