<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/6/18
 * Time: 2:55 PM
 */


namespace PapaLocal\ReferralAgreement\Notification;


use PapaLocal\Entity\User;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;


/**
 * Class NotificationFactory
 *
 * @package PapaLocal\ReferralAgreement\Notification
 */
class NotificationFactory
{
    /**
     * @param string $recipient
     * @param string $oldName
     * @param string $newName
     *
     * @return AgreementNameChange
     */
    public function newAgreementNameChanged(string $recipient, string $oldName, string $newName): AgreementNameChange
    {
        return new AgreementNameChange($recipient, $oldName, $newName);
    }

    /**
     * @param string $recipient
     * @param string $agreementName
     * @param string $field
     * @param string $oldValue
     * @param string $newValue
     *
     * @return AgreementChanged
     */
    public function newAgreementChanged(string $recipient, string $agreementName, string $field, string $oldValue, string $newValue): AgreementChanged
    {
        return new AgreementChanged($recipient, $agreementName, $field, $oldValue, $newValue);
    }

    /**
     * @param string          $recipient
     * @param string          $agreementName
     * @param AgreementStatus $status
     *
     * @return AgreementStatusChanged
     */
    public function newAgreementStatusChanged(string $recipient, string $agreementName, AgreementStatus $status): AgreementStatusChanged
    {
        return new AgreementStatusChanged($recipient, $agreementName, $status);
    }

    /**
     * @param string $inviteeFirstName
     * @param string $inviteeLastName
     *
     * @return InvitationAccepted
     */
    public function newInvitationAccepted(string $inviteeFirstName, string $inviteeLastName): InvitationAccepted
    {
        return new InvitationAccepted($inviteeFirstName, $inviteeLastName);
    }

    /**
     * @param string             $recipient
     * @param string             $agreementName
     * @param string             $listType
     * @param IncludeExcludeList $list
     *
     * @return AgreementListUpdated
     */
    public function newAgreementListUpdated(string $recipient, string $agreementName, string $listType, IncludeExcludeList $list): AgreementListUpdated
    {
        return new AgreementListUpdated($recipient, $agreementName, $listType, $list);
    }
}