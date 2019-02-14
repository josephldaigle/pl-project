<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/31/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


/**
 * Class ActivateAgreement.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class ActivateAgreement
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var string requesting user's userGuid
     */
    private $requestorGuid;

    /**
     * ActivateAgreement constructor.
     *
     * @param string $agreementGuid
     * @param string $reason
     * @param string $requestorGuid
     */
    public function __construct(string $agreementGuid, string $reason, string $requestorGuid)
    {
        $this->agreementGuid = $agreementGuid;
        $this->reason = $reason;
        $this->requestorGuid = $requestorGuid;
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
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getRequestorGuid(): string
    {
        return $this->requestorGuid;
    }
}