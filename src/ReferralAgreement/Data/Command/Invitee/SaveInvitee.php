<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/3/18
 * Time: 7:21 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;


/**
 * Class SaveInvitee
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class SaveInvitee
{
    /**
     * @var ReferralAgreementInvitee
     */
    private $referralAgreementInvitee;

    /**
     * SaveAgreementInvitee constructor.
     *
     * @param ReferralAgreementInvitee $referralAgreementInvitee
     */
    public function __construct(ReferralAgreementInvitee $referralAgreementInvitee)
    {
        $this->referralAgreementInvitee = $referralAgreementInvitee;
    }

    /**
     * @return string
     */
    public function getInviteeGuid(): string
    {
        return $this->referralAgreementInvitee->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->referralAgreementInvitee->getAgreementId()->value();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->referralAgreementInvitee->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->referralAgreementInvitee->getLastName();
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->referralAgreementInvitee->getMessage();
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->referralAgreementInvitee->getEmailAddress()->getEmailAddress();
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        if (is_null($this->referralAgreementInvitee->getPhoneNumber())) {
            return '';
        }
        return $this->referralAgreementInvitee->getPhoneNumber()->getPhoneNumber();
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return ($this->referralAgreementInvitee->isUser()) ? $this->referralAgreementInvitee->getUserId()->value() : '';
    }
}