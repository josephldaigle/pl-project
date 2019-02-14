<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */

namespace PapaLocal\ReferralAgreement\Data\Query\Invitee;

/**
 * Class FindOne.
 *
 * @package PapaLocal\ReferralAgreement\Data\Query\Invitee
 */
class FindOne
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $inviteeEmailAddress;

    /**
     * FindOne constructor.
     *
     * @param string $agreementGuid
     * @param string $inviteeEmailAddress
     */
    public function __construct(string $agreementGuid, string $inviteeEmailAddress)
    {
        $this->agreementGuid = $agreementGuid;
        $this->inviteeEmailAddress = $inviteeEmailAddress;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid;
    }

    /**
     * @return string
     */
    public function getInviteeEmailAddress(): string
    {
        return $this->inviteeEmailAddress;
    }
}