<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateQuantity.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateQuantity
{
    /**
     * @var GuidInterface
     */
    private $agreementGuid;

    /**
     * @var int
     */
    private $quantity;

    /**
     * UpdateQuantity constructor.
     *
     * @param GuidInterface $agreementGuid
     * @param int           $quantity
     */
    public function __construct(GuidInterface $agreementGuid, int $quantity)
    {
        $this->agreementGuid = $agreementGuid;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid->value();
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}