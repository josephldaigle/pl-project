<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 10:25 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;


/**
 * Class UpdateAgreementStatus.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementStatus
{
    /**
     * @var AgreementStatus
     */
    private $agreementStatus;

    /**
     * UpdateAgreementStatus constructor.
     *
     * @param AgreementStatus $agreementStatus
     */
    public function __construct(AgreementStatus $agreementStatus)
    {
        $this->agreementStatus = $agreementStatus;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementStatus->getAgreementId()->value();
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->agreementStatus->getStatus()->getValue();
    }

    /**
     * @return string
     */
    public function getReason(): string
    {
        return $this->agreementStatus->getReason()->getValue();
    }

    /**
     * @return string
     */
    public function getAuthorGuid(): string
    {
        return $this->agreementStatus->getUpdater()->value();
    }
}