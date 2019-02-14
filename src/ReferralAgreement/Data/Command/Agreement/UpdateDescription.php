<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:54 AM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateDescription
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
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
    private $description;

    /**
     * UpdateDescription constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param string        $description
     */
    public function __construct(GuidInterface $agreementGuid, string $description)
    {
        $this->agreementGuid = $agreementGuid;
        $this->description   = $description;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

}