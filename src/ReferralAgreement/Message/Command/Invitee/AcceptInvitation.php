<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/14/18
 * Time: 9:16 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class AcceptInvitation
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class AcceptInvitation
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * JoinAgreement constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $userGuid
     */
    public function __construct(GuidInterface $agreementGuid, GuidInterface $userGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->userGuid      = $userGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }
}