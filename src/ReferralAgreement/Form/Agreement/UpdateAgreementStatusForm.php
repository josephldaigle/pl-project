<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/2/18
 * Time: 7:23 AM
 */

namespace PapaLocal\ReferralAgreement\Form\Agreement;


/**
 * Class UpdateAgreementStatusForm
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateAgreementStatusForm
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $status;

    /**
     * UpdateAgreementStatusForm constructor.
     *
     * @param string $agreementGuid
     * @param string $status
     */
    public function __construct(string $agreementGuid, string $status)
    {
        $this->agreementGuid = $agreementGuid;
        $this->status        = $status;
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
    public function getStatus(): string
    {
        return $this->status;
    }

}