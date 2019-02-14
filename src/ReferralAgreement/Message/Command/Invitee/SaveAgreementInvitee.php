<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm;


/**
 * Class SaveAgreementInvitee
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class SaveAgreementInvitee
{
    /**
     * @var GuidInterface
     */
    private $inviteeGuid;

    /**
     * @var ReferralAgreementInviteeForm
     */
    private $form;

    /**
     * SaveAgreementInvitee constructor.
     *
     * @param GuidInterface                $inviteeGuid
     * @param ReferralAgreementInviteeForm $form
     */
    public function __construct(GuidInterface $inviteeGuid, ReferralAgreementInviteeForm $form)
    {
        $this->inviteeGuid = $inviteeGuid;
        $this->form        = $form;
    }

    public function getInviteeGuid(): GuidInterface
    {
        return $this->inviteeGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->form->getAgreementId();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->form->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->form->getLastName();
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->form->getMessage();
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->form->getEmailAddress();
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->form->getPhoneNumber();
    }
}