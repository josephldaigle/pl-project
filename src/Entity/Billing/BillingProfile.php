<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/28/17
 */

namespace PapaLocal\Entity\Billing;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Entity;


/**
 * Class BillingProfile.
 */
class BillingProfile extends Entity
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var int $userId
     */
    private $userId;

    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var int $customerId
     *
     */
    private $customerId;

    /**
     * @var float the dollar amount at which the user's account is auto-refilled
     */
    private $minBalance;

    /**
     * @var float the amount the auto-refill will bring the account to
     */
    private $maxBalance;

    /**
     * @var float the user's current settled balance
     */
    private $balance;

    /**
     * @var float the amount the user has available to withdraw
     */
    private $availableBalance;

    /**
     * @var bool $isActive
     */
    private $isActive;

    /**
     * @var Collection
     */
    private $paymentProfile;

	/**
	 * @var TransactionList
	 */
    private $transactionList;

	/**
	 * @var PastYearTransactionSummary
	 */
    private $pastYearTransactionSummary;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return BillingProfile
     */
    public function setId(int $id): BillingProfile
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return BillingProfile
     */
    public function setUserId(int $userId): BillingProfile
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return BillingProfile
     */
    public function setUserGuid(GuidInterface $userGuid): BillingProfile
    {
        $this->userGuid = $userGuid;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return BillingProfile
     */
    public function setCustomerId(int $customerId): BillingProfile
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return float
     */
    public function getMinBalance()
    {
        return $this->minBalance;
    }

    /**
     * @param float $minBalance
     * @return BillingProfile
     */
    public function setMinBalance(float $minBalance): BillingProfile
    {
        $this->minBalance = $minBalance;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxBalance()
    {
        return $this->maxBalance;
    }

    /**
     * @param float $maxBalance
     * @return BillingProfile
     */
    public function setMaxBalance(float $maxBalance): BillingProfile
    {
        $this->maxBalance = $maxBalance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance ? $this->balance : 0.00;
    }

    /**
     * @param float $balance
     * @return BillingProfile
     */
    public function setBalance(float $balance): BillingProfile
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return float
     */
    public function getAvailableBalance(): float
    {
        return $this->availableBalance ? $this->availableBalance : 0.00;
    }

    /**
     * @param float $availableBalance
     *
     * @return BillingProfile
     */
    public function setAvailableBalance(float $availableBalance): BillingProfile
    {
        $this->availableBalance = $availableBalance;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return ($this->isActive) ? true : false;
    }

    /**
     * @param bool $isActive
     *
     * @return BillingProfile
     */
    public function setIsActive(bool $isActive): BillingProfile
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasDefaultPaymentProfile(): bool
    {
        if ($this->paymentProfile->getDefaultPayProfile()) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getPaymentProfile()
    {
        return $this->paymentProfile;
    }

    /**
     * @param mixed $paymentProfile
     *
     * @return BillingProfile
     */
    public function setPaymentProfile(PaymentProfile $paymentProfile): BillingProfile
    {
        $this->paymentProfile = $paymentProfile;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getTransactionList()
	{
		return $this->transactionList;
	}

	/**
	 * @param TransactionList $transactionList
	 *
	 * @return BillingProfile
	 */
	public function setTransactionList( TransactionList $transactionList ): BillingProfile
	{
		$this->transactionList = $transactionList;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPastYearTransactionSummary()
	{
		return $this->pastYearTransactionSummary;
	}

	/**
	 * @param PastYearTransactionSummary $pastYearTransactionSummary
	 *
	 * @return BillingProfile
	 */
	public function setPastYearTransactionSummary(PastYearTransactionSummary $pastYearTransactionSummary): BillingProfile
	{
		$this->pastYearTransactionSummary = $pastYearTransactionSummary;
		return $this;
	}


}