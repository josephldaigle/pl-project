<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Agreement;

/**
 * Class UpdateQuantity.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Agreement
 */
class UpdateQuantity
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var int
     */
    private $quantity;

    /**
     * UpdateQuantity constructor.
     *
     * @param string $agreementGuid
     * @param int    $quantity
     */
    public function __construct(string $agreementGuid, int $quantity)
    {
        $this->agreementGuid = $agreementGuid;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}