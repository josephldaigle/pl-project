<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */


namespace PapaLocal\ReferralAgreement\Form\Agreement;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateReferralPriceForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateReferralPriceForm
{
    /**
     * @var string
     */
    private $agreementGuid;

    /**
     * @var float
     *
     *
     * @Assert\NotBlank(
     *     message = "Price cannot be blank."
     *     )
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Price must be greater than 0."
     *     )
     */
    private $price;

    /**
     * UpdateReferralPriceForm constructor.
     *
     * @param string $agreementGuid
     * @param string  $price
     */
    public function __construct(string $agreementGuid = '', string $price = '')
    {
        $this->agreementGuid = $agreementGuid;
        $this->price = floatval($price);
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        return $this->agreementGuid;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}