<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/1/19
 * Time: 10:08 PM
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Transaction.
 *
 * @package PapaLocal\Billing\ValueObject
 */
class Transaction implements TransactionInterface
{
    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var int
     */
    private $billingProfileId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $month;

    /**
     * @var int
     */
    private $referralId;

    /**
     * @var int
     */
    private $payMethodId;

    /**
     * @var TransactionType
     */
    private $type;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var string a transaction ID for 3rd party system synchronization
     */
    private $transactionId;

    /**
     * @var string
     */
    private $transactionTime;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * Transaction constructor.
     *
     * @param GuidInterface   $guid
     * @param GuidInterface   $userGuid
     * @param int             $billingProfileId
     * @param string          $description
     * @param float           $amount
     * @param TransactionType $type
     * @param float           $balance
     * @param string          $month
     * @param string          $transactionTime
     * @param string          $timeCreated
     * @param int|null        $userId
     * @param int|null        $payMethodId
     * @param string|null     $transactionId
     * @param int|null        $referralId
     */
    public function __construct(GuidInterface $guid,
                                GuidInterface $userGuid,
                                int $billingProfileId,
                                string $description,
                                float $amount,
                                TransactionType $type,
                                float $balance = 0.00,
                                string $month = '',
                                string $transactionTime = '',
                                string $timeCreated = '',
                                int $userId = null,
                                int $payMethodId = null,
                                string $transactionId = '',
                                int $referralId = null
                                )
    {
        $this->guid = $guid;
        $this->userId = $userId;
        $this->userGuid = $userGuid;
        $this->billingProfileId = $billingProfileId;
        $this->description = $description;
        $this->amount = $amount;
        $this->month = $month;
        $this->referralId = $referralId;
        $this->payMethodId = $payMethodId;
        $this->type = $type;
        $this->balance = $balance;
        $this->transactionId = $transactionId;
        $this->transactionTime = $transactionTime;
        $this->timeCreated = $timeCreated;
    }

    /**
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }

    /**
     * @return int
     */
    public function getBillingProfileId(): int
    {
        return $this->billingProfileId;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @return mixed
     */
    public function getReferralId()
    {
        return $this->referralId;
    }

    /**
     * @return mixed
     */
    public function getPayMethodId()
    {
        return $this->payMethodId;
    }

    /**
     * @return TransactionType
     */
    public function getType(): TransactionType
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function getTransactionTime(): string
    {
        return $this->transactionTime;
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }
}