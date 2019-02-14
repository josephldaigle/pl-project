<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */


namespace PapaLocal\ReferralAgreement\Form\Agreement;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateStrategyForm.
 *
 * @package PapaLocal\ReferralAgreement\Form\Agreement
 */
class UpdateStrategyForm
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
     * @var string
     *
     * @Assert\Choice(
     *     choices = {"weekly", "monthly"},
     *     message = "Strategy is not valid."
     *     )
     */
    private $strategy;

    /**
     * UpdateStrategyForm constructor.
     *
     * @param string $agreementGuid
     * @param string $strategy
     */
    public function __construct(string $agreementGuid = '', string $strategy = '')
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