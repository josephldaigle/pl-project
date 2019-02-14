<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/13/18
 */

namespace PapaLocal\Entity\Billing;

use PapaLocal\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 *
 * @package PapaLocal\Entity\Billing
 */
class Transaction extends Entity
{
	public const TYPE_CREDIT = 'credit';
	public const TYPE_DEBIT = 'debit';

	public const DESC_REFERRAL = 'Referral Purchase';
	public const DESC_WITHDRAW = 'Cash Withdrawal';
	public const DESC_DEPOSIT = 'Cash Deposit';
	public const DESC_REFUND = 'Cash Refund';

    /**
     * @var int
     *
     * @Assert\Blank(
     *     message = "Transaction ID must be blank.",
     *     groups = {"create"}
     *  )
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "Billing profile ID must be present.",
     *     groups = {"create", "display"}
     * )
     */
    private $billingProfileId;

    /**
     * @var int
     *
     * @Assert\NotBlank(
     *     message = "User ID must be present.",
     *     groups = {"create", "display"}
     * )
     */
    private $userId;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Description must be present.",
     *     groups = {"create", "display"}
     * )
     */
    private $description;

    /**
     * @var float
     *
     * @Assert\NotBlank(
     *     message = "Transaction amount must be present.",
     *     groups = {"create", "display"}
     * )
     */
    private $amount;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Transaction type must be present.",
     *     groups = {"create", "display"}
     * )
     */
    private $type;

    /**
     * @var float
     *
     * @Assert\Blank(
     *     message = "Transaction balance must be blank.",
     *     groups = {"create"}
     * )
     *
     * @Assert\NotBlank(
     *     message = "Transaction balance must be present.",
     *     groups = {"display"}
     * )
     */
    private $balance;

    /**
     * @var string  the authorize.net transaction ID
     *
     * @Assert\NotBlank(
     *     message = "Authorize.net transaction ID must be present.",
     *     groups = {"create"}
     * )
     */
    private $aNetTransId;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Transaction balance must be blank.",
     *     groups = {"create"}
     * )
     */
    private $timeFinalized;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time created must be blank.",
     *     groups = {"create"}
     *  )
     *
     * @Assert\NotBlank(
     *     message = "Time created must be present.",
     *     groups = {"display"}
     *  )
     */
    private $timeCreated;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Transaction
     */
    public function setId(int $id): Transaction
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBillingProfileId()
    {
        return $this->billingProfileId;
    }

    /**
     * @param int $billingProfileId
     * @return Transaction
     */
    public function setBillingProfileId(int $billingProfileId): Transaction
    {
        $this->billingProfileId = $billingProfileId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return Transaction
     */
    public function setUserId(int $userId): Transaction
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Transaction
     */
    public function setDescription(string $description): Transaction
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Transaction
     */
    public function setAmount(float $amount): Transaction
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Transaction
     */
    public function setType(string $type): Transaction
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     * @return Transaction
     */
    public function setBalance(float $balance): Transaction
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getANetTransId()
    {
        return $this->aNetTransId;
    }

    /**
     * @param string $aNetTransId
     * @return Transaction
     */
    public function setANetTransId(string $aNetTransId): Transaction
    {
        $this->aNetTransId = $aNetTransId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeFinalized()
    {
        return $this->timeFinalized;
    }

    /**
     * @param string $timeFinalized
     * @return Transaction
     */
    public function setTimeFinalized(string $timeFinalized): Transaction
    {
        $this->timeFinalized = $timeFinalized;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     * @return Transaction
     */
    public function setTimeCreated(string $timeCreated): Transaction
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

}