<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */

namespace PapaLocal\ReferralAgreement\ValueObject\Invitee;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class InviteeIdentifier.
 *
 * Model an identification for an Invitee.
 *
 * @package PapaLocal\ReferralAgreement\ValueObject\Invitee
 */
class InviteeIdentifier
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var EmailAddress
     */
    private $inviteeEmailAddress;

    /**
     * InviteeIdentifier constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param EmailAddress  $inviteeEmailAddress
     */
    public function __construct(GuidInterface $agreementGuid, EmailAddress $inviteeEmailAddress)
    {
        $this->agreementGuid = $agreementGuid;
        $this->inviteeEmailAddress = $inviteeEmailAddress;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return EmailAddress
     */
    public function getInviteeEmailAddress(): EmailAddress
    {
        return $this->inviteeEmailAddress;
    }
}