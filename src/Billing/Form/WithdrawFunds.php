<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/15/19
 */


namespace PapaLocal\Billing\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * Class WithdrawFunds.
 *
 * @package PapaLocal\Billing\Form
 */
class WithdrawFunds
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Id must be present."
     * )
     */
    private $userGuid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Amount must be present."
     * )
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message = "Please specify the amount you would like to withdraw."
     * )
     */
    private $amount;

    /**
     * WithdrawFunds constructor.
     * @param string|null $userGuid
     * @param string|null $amount
     */
    public function __construct(string $userGuid = null,
                                string $amount = null)
    {
        $this->userGuid = $userGuid;
        $this->amount = $amount;
    }


    /**
     * @return mixed
     */
    public function getUserGuid()
    {
        return $this->userGuid;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }
}