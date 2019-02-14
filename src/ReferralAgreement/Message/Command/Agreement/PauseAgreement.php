<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/1/18
 * Time: 11:40 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;

use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;


/**
 * Class PauseAgreement
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class PauseAgreement
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var StatusChangeReason
     */
    private $changeReason;

    /**
     * Guid of user requesting change.
     *
     * @var GuidInterface
     */
    private $requestorGuid;

    /**
     * PauseAgreement constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param StatusChangeReason $changeReason
     * @param GuidInterface $requestorGuid
     */
    public function __construct(
        GuidInterface $agreementGuid,
        StatusChangeReason $changeReason,
        GuidInterface $requestorGuid
    )
    {
        $this->agreementGuid = $agreementGuid;
        $this->changeReason  = $changeReason;
        $this->requestorGuid = $requestorGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return StatusChangeReason
     */
    public function getChangeReason(): StatusChangeReason
    {
        return $this->changeReason;
    }

    /**
     * @return GuidInterface
     */
    public function getRequestorGuid(): GuidInterface
    {
        return $this->requestorGuid;
    }
}