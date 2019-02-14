<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/12/18
 * Time: 11:50 AM
 */

namespace PapaLocal\Referral\Notification;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\User;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;
use PapaLocal\Referral\Entity\RecipientInterface;
use PapaLocal\Referral\Notification\Message\ReferralAcquisition;
use PapaLocal\Referral\Notification\Message\ReferralAcquisitionConfirmation;
use PapaLocal\Referral\Notification\Message\ReferralDisputeNotice;
use PapaLocal\Referral\Notification\Message\ReferralDisputeConfirmation;
use PapaLocal\Referral\Notification\Message\ReferralFinalizationConfirmation;
use PapaLocal\Referral\Notification\Message\ReferralDispute;
use PapaLocal\Referral\Notification\Message\ReferralFinalization;
use PapaLocal\Referral\Notification\Message\ReferralInvitationConfirmation;
use PapaLocal\Referral\ValueObject\ContactRecipient;


/**
 * Class NotificationFactory
 * @package PapaLocal\Referral\Notification
 */
class NotificationFactory
{
    /**
     * @param ContactRecipient $recipient
     * @param User $provider
     * @return ReferralInvitationConfirmation
     */
    public function newReferralInvitationConfirmation(ContactRecipient $recipient, User $provider)
    {
        return new ReferralInvitationConfirmation($recipient, $provider);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralAcquisition
     */
    public function newReferralAcquisition(User $recipient, User $provider)
    {
        return new ReferralAcquisition($recipient, $provider);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralAcquisitionConfirmation
     */
    public function newReferralAcquisitionConfirmation(User $recipient, User $provider)
    {
        return new ReferralAcquisitionConfirmation($recipient, $provider);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralDispute
     */
    public function newReferralDispute(User $recipient, User $provider)
    {
        return new ReferralDispute($recipient, $provider);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralDisputeConfirmation
     */
    public function newReferralDisputeConfirmation(User $recipient, User $provider)
    {
        return new ReferralDisputeConfirmation($recipient, $provider);
    }

    /**
     * @param AssociateFeedItem $associateFeedItem
     * @return ReferralDisputeNotice
     */
    public function newReferralDisputeNotice(AssociateFeedItem $associateFeedItem)
    {
        return new ReferralDisputeNotice($associateFeedItem);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralFinalization
     */
    public function newReferralFinalization(User $recipient, User $provider)
    {
        return new ReferralFinalization($recipient, $provider);
    }

    /**
     * @param User $recipient
     * @param User $provider
     * @return ReferralFinalizationConfirmation
     */
    public function newReferralFinalizationConfirmation(User $recipient, User $provider)
    {
        return new ReferralFinalizationConfirmation($recipient, $provider);
    }
}