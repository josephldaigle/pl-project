<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/3/18
 * Time: 9:01 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class LoadInviteeAgreements
 *
 * @package PapaLocal\ReferralAgreement\Message\Query\Agreement
 */
class LoadInviteeAgreements
{
    /**
     * @var GuidInterface
     */
    private $inviteeUserGuid;

    /**
     * LoadInviteeAgreements constructor.
     *
     * @param GuidInterface $inviteeUserGuid
     */
    public function __construct(GuidInterface $inviteeUserGuid)
    {
        $this->inviteeUserGuid = $inviteeUserGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getInviteeUserGuid(): GuidInterface
    {
        return $this->inviteeUserGuid;
    }

}