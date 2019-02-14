<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 8/29/18
 * Time: 9:37 AM
 */


namespace PapaLocal\Billing\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class DepositFunds
 *
 * @package PapaLocal\Billing\Form
 */
class DepositFunds
{
    /**
     * @var float
     *
     * @Assert\NotBlank(
     *     message = "Amount cannot be empty."
     * )
     * @Assert\Type(
     *     type = "float",
     *     message = "Amount must be a real number."
     * )
     * @Assert\Range(
     *     min = 30.00,
     *     max = 9999.99,
     *     minMessage = "The minimum required deposit is ${{ limit }}.",
     *     maxMessage = "The maximum deposit amount ${{ limit }}."
     * )
     */
    private $amount;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "A payment method must be selected."
     * )
     */
    private $accountId;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The account must have a type."
     * )
     */
    private $accountType;

    public function __construct(float $amount, string $accountId, string $accountType)
    {
        $this->setAmount($amount);
        $this->setAccountId($accountId);
        $this->setAccountType($accountType);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return mixed
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param float $amount
     */
    protected function setAmount(float $amount)
    {
        $this->amount = (float) $amount;
    }

    /**
     * @param string $accountId
     */
    protected function setAccountId(string $accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @param string $accountType
     */
    protected function setAccountType(string $accountType)
    {
        $this->accountType = $accountType;
    }
}