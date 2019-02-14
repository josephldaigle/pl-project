<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/16/18
 */

namespace PapaLocal\ReferralAgreement\Form\Invitee;


use PapaLocal\Core\ValueObject\Guid;


/**
 * Class InvitationResponseForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Invitee
 */
class InvitationResponseForm
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $inviteeGuid;

    /**
     * InvitationResponseForm constructor.
     *
     * @param string $agreementGuid
     * @param string $inviteeGuid
     */
    public function __construct(string $agreementGuid, string $inviteeGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->inviteeGuid = $inviteeGuid;
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
    public function getInviteeGuid(): string
    {
        return $this->inviteeGuid;
    }
}