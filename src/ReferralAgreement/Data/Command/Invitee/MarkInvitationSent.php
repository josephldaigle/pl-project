<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/11/18
 * Time: 4:17 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class MarkInvitationSent
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class MarkInvitationSent
{
    /**
     * @var GuidInterface
     */
    private $invitationGuid;

    /**
     * MarkInvitationSent constructor.
     *
     * @param GuidInterface $invitationGuid
     */
    public function __construct(GuidInterface $invitationGuid)
    {
        $this->invitationGuid = $invitationGuid;
    }

    /**
     * @return string
     */
    public function getInvitationGuid(): string
    {
        return $this->invitationGuid->value();
    }
}