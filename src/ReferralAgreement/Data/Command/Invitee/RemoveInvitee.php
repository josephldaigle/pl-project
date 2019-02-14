<?php
/**
 * Created by eWebify, LLC.
 * Creator: Joe Daigle
 * Date: 2/6/19
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class RemoveInvitee.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class RemoveInvitee
{
    /**
     * @var GuidInterface
     */
    private $inviteeGuid;

    /**
     * RemoveInvitee constructor.
     *
     * @param GuidInterface $inviteeGuid
     */
    public function __construct(GuidInterface $inviteeGuid)
    {
        $this->inviteeGuid = $inviteeGuid;
    }

    /**
     * @return string
     */
    public function getInviteeGuid(): string
    {
        return $this->inviteeGuid->value();
    }
}