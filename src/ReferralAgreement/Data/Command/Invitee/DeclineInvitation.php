<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 7:49 AM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class DeclineInvitation
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class DeclineInvitation
{
    /**
     * @var GuidInterface
     */
    private $invitationGuid;

    /**
     * DeclineInvitation constructor.
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