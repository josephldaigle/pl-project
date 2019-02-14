<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */


namespace PapaLocal\ReferralAgreement\Form\Agreement;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateQuantityForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateQuantityForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Agreement id cannot be blank."
     *     )
     */
    private $agreementGuid;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Quantity cannot be blank."
     *     )
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Quantity must be 1 or more."
     *     )
     */
    private $quantity;

    /**
     * UpdateQuantityForm constructor.
     *
     * @param string $agreementGuid
     * @param int    $quantity
     */
    public function __construct(string $agreementGuid = '', int $quantity = null)
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
     * @return mixed int|null
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}