<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 11:07 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


/**
 * Class AssignUserGuid
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class AssignUserGuid
{
    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $userGuid;

    /**
     * AssignUserGuid constructor.
     *
     * @param string $emailAddress
     * @param string $userGuid
     */
    public function __construct($emailAddress, $userGuid)
    {
        $this->emailAddress = $emailAddress;
        $this->userGuid     = $userGuid;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }
}