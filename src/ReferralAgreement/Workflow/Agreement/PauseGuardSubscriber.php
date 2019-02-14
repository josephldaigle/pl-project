<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/2/18
 * Time: 8:26 PM
 */

namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;


/**
 * Class PauseGuardSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class PauseGuardSubscriber implements EventSubscriberInterface
{
    /**
     * @param GuardEvent $event
     */
    public function guardReview(GuardEvent $event)
    {
        $agreement = $event->getSubject();
        $status = $agreement->getStatusHistory()->getCurrentStatus();

        switch($status->getReason()) {
            case StatusChangeReason::REFERRAL_QUOTA_REACHED():
                //TODO: Implement
                break;
            case StatusChangeReason::OWNER_REQUESTED():
                //TODO: Implement
                break;
            case StatusChangeReason::INSUFFICIENT_FUNDS():
                //TODO: Implement
                break;
        }
        return;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
//            'workflow.referral_agreement.guard.pause' => 'guardReview'
        ];
    }

}