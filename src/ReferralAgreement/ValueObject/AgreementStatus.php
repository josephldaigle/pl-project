<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/18/18
 */


namespace PapaLocal\ReferralAgreement\ValueObject;


use PapaLocal\Core\ValueObject\Guid;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Class ReferralAgreementStatus
 *
 * @package PapaLocal\ReferralAgreement\ValueObject
 */
class AgreementStatus
{
    /**
     * @var Guid
     */
    private $agreementId;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var StatusChangeReason
     */
    private $reason;

    /**
     * @var Guid
     */
    private $updater;

    /**
     * @var string
     */
    private $timeUpdated;

    /**
     * AgreementStatus constructor.
     *
     * @param Guid               $agreementId
     * @param Status             $status
     * @param StatusChangeReason $reason
     * @param string             $timeUpdated
     * @param Guid               $updater
     */
    public function __construct(
        Guid $agreementId,
        Status $status,
        StatusChangeReason $reason,
        Guid $updater,
        string $timeUpdated = ''
    )
    {
        $this->setAgreementId($agreementId);
        $this->setStatus($status);
        $this->setReason($reason);
        $this->setUpdater($updater);
        $this->setTimeUpdated($timeUpdated);
    }

    /**
     * @return Guid
     */
    public function getAgreementId(): Guid
    {
        return $this->agreementId;
    }

    /**
     * @param Guid $agreementId
     *
     * @return AgreementStatus
     */
    protected function setAgreementId(Guid $agreementId): AgreementStatus
    {
        $this->agreementId = $agreementId;

        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     *
     * @return AgreementStatus
     */
    protected function setStatus(Status $status): AgreementStatus
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return StatusChangeReason
     */
    public function getReason(): StatusChangeReason
    {
        return $this->reason;
    }

    /**
     * @param StatusChangeReason $reason
     *
     * @return AgreementStatus
     */
    protected function setReason(StatusChangeReason $reason): AgreementStatus
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return Guid
     */
    public function getUpdater(): Guid
    {
        return $this->updater;
    }

    /**
     * @param Guid $updater
     *
     * @return AgreementStatus
     */
    protected function setUpdater(Guid $updater): AgreementStatus
    {
        $this->updater = $updater;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeUpdated(): string
    {
        return $this->timeUpdated;
    }

    /**
     * @param string $timeUpdated
     *
     * @return AgreementStatus
     */
    protected function setTimeUpdated(string $timeUpdated): AgreementStatus
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }
}