<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateReferralPrice.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateReferralPrice
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var float
     */
    private $price;

    /**
     * UpdateReferralPrice constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param float         $price
     */
    public function __construct(GuidInterface $agreementGuid, float $price)
    {
        $this->agreementGuid = $agreementGuid;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}