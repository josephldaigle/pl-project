<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/29/18
 */


namespace PapaLocal\Billing\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateRechargeSetting.
 *
 * @package PapaLocal\Billing\Form
 */
class UpdateRechargeSetting
{
    /**
     * @var  string
     */
    private $userGuid;

    /**
     * @var float
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="The minimum balance must be greater than zero."
     * )
     *
     * @Assert\Expression(
     *     "this.getMinBalance() < this.getMaxBalance()",
     *     message="The minimum balance must be less than the maximum balance."
     * )
     */
    private $minBalance;

    /**
     * @var float
     *
     * @Assert\Expression(
     *     "(this.getMaxBalance() - this.getMinBalance()) < 9999",
     *     message="The recharge amount cannot exceed $9,999"
     * )
     *
     */
    private $maxBalance;

    /**
     * UpdateRechargeSetting constructor.
     *
     * @param float $minBalance
     * @param float $maxBalance
     */
    public function __construct(float $minBalance, float $maxBalance)
    {
        $this->minBalance = $minBalance;
        $this->maxBalance = $maxBalance;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }

    /**
     * @return float
     */
    public function getMinBalance(): float
    {
        return $this->minBalance;
    }

    /**
     * @return float
     */
    public function getMaxBalance(): float
    {
        return $this->maxBalance;
    }

}