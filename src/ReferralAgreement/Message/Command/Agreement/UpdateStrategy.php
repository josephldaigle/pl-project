<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;

/**
 * Class UpdateStrategy.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateStrategy
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var string
     */
    private $strategy;

    /**
     * UpdateStrategy constructor.
     *
     * @param string $agreementGuid
     * @param string $strategy
     */
    public function __construct(string $agreementGuid, string $strategy)
    {
        $this->agreementGuid = $agreementGuid;
        $this->strategy = $strategy;
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
    public function getStrategy(): string
    {
        return $this->strategy;
    }
}