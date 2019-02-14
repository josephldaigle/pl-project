<?php
/**
 * Created by eWebify, LLC.
 * Creator: Joe Daigle
 * Date: 2/6/19
 */


namespace PapaLocal\ReferralAgreement\Form\Invitee;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * RemoveInviteeForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Invitee
 */
class RemoveInviteeForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Id cannot be blank."
     *     )
     */
    private $agreementGuid;
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Id cannot be blank."
     *     )
     */
    private $inviteeGuid;

    /**
     * RemoveInviteeForm constructor.
     *
     * @param string $agreementGuid
     * @param string $inviteeGuid
     */
    public function __construct(string $agreementGuid = '', string $inviteeGuid = '')
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