<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:42 AM
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateDescription
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateDescription
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $agreementDescription;

    /**
     * UpdateDescription constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param string        $agreementDescription
     */
    public function __construct(GuidInterface $agreementGuid, $agreementDescription)
    {
        $this->agreementGuid        = $agreementGuid;
        $this->agreementDescription = $agreementDescription;
    }

    /**
     * @return GuidInterface
     */
    public function getAgreementGuid(): GuidInterface
    {
        return $this->agreementGuid;
    }

    /**
     * @return string
     */
    public function getAgreementDescription(): string
    {
        return $this->agreementDescription;
    }
}