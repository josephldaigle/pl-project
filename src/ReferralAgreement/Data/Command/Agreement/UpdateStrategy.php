<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateStrategy.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateStrategy
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $strategy;

    /**
     * UpdateStrategy constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param string        $strategy
     */
    public function __construct(GuidInterface $agreementGuid, string $strategy)
    {
        $this->agreementGuid = $agreementGuid;
        $this->strategy = $strategy;
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
    public function getStrategy(): string
    {
        return $this->strategy;
    }
}