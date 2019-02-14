<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;

use PapaLocal\Core\ValueObject\GuidInterface;

/**
 * Class DeclineInvitation.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class DeclineInvitation
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
     * DeclineInvitation constructor.
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