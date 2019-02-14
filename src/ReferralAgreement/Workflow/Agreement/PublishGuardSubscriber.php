<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/10/18
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\Billing\Message\MessageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;


/**
 * Class PublishGuardSubscriber
 *
 * Guards against a ReferralAgreement being activated when expected
 * business requirements are not met.
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class PublishGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
	private $messageBus;

    /**
     * @var MessageFactory
     */
	private $messageFactory;

    /**
     * PublishGuardSubscriber constructor.
     *
     * @param MessageBusInterface $messageBus
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        MessageBusInterface $messageBus,
        MessageFactory $messageFactory
    )
    {
        $this->messageBus         = $messageBus;
        $this->messageFactory     = $messageFactory;
    }

    /**
	 * @param GuardEvent $event
	 *
	 * @return GuardEvent
	 */
    public function guardReview(GuardEvent $event)
    {
    	// default to blocked (prevents agreement from changing state)
	    $event->setBlocked(false);

        try {
            // request billing profile from domain
            $loadBillProQry = $this->messageFactory->newLoadUserBillingProfile($event->getSubject()->getOwnerGuid()->value());
            $billingProfile = $this->messageBus->dispatch($loadBillProQry);

        } catch (\Exception $exception) {
            // no billing profile exists
            $event->addTransitionBlocker(new TransitionBlocker('You need to have a default payment method, so that you can pay for referrals.', PublishGuardBlockCode::BLOCK_PAY_METHOD));

            return $event;
        }

        // check if user has default payment profile
        if (! $billingProfile->hasDefaultPaymentProfile()) {
            // user does not have a default pay method
            $event->addTransitionBlocker(new TransitionBlocker('You need to have a default payment method, so that you can pay for referrals.', PublishGuardBlockCode::BLOCK_PAY_METHOD));
        }

        // check user has adequate balance
        $requiredBalance = ($event->getSubject()->getBid() * 3);

        if (! ($billingProfile->getBalance() >= $requiredBalance)) {
            $event->addTransitionBlocker(new TransitionBlocker(
                sprintf('You need a balance of at least $%.2f to pay for your initial referrals.', $requiredBalance),
                PublishGuardBlockCode::BLOCK_ACCT_BAL,
                array(
                    'currentBalance'  => $billingProfile->getBalance(),
                    'requiredBalance' => $requiredBalance,
                ))
            );
        }

	    // check that the agreement has invitees
	    $agreementHasInvitees = (($event->getSubject()->getNumberInvitees() + $event->getSubject()->getNumberParticipants()) > 0);

	    // allow publishing if user has balance and agreement has invitees
	    if (! $agreementHasInvitees) {
	    	$event->addTransitionBlocker(new TransitionBlocker('You need to invite at least one person to sell you referrals.', PublishGuardBlockCode::BLOCK_PARTICIPANT));
	    }

	    // check that the referrals received this period does not exceed quantity
        if ($event->getSubject()->getReferralCount() >= $event->getSubject()->getQuantity()) {
	        $event->addTransitionBlocker(new TransitionBlocker('This agreement has reached it\'s quota. Increase the quantity to start receiving referrals again.', PublishGuardBlockCode::BLOCK_QUOTA_REACHED));
        }

	    return $event;
    }

	/**
	 * {@inheritdoc}
	 */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_agreement.guard.publish' => 'guardReview',
            'workflow.referral_agreement.guard.activate' => 'guardReview'
        ];
    }
}