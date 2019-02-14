<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/14/18
 * Time: 9:28 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


/**
 * Class AcceptInvitation.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class AcceptInvitation
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $userGuid;

    /**
     * AcceptInvitation constructor.
     *
     * @param string $agreementGuid
     * @param string $userGuid
     */
    public function __construct(string $agreementGuid, string $userGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->userGuid = $userGuid;
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
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }
}