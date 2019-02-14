<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/1/19
 * Time: 10:04 PM
 */


namespace PapaLocal\Billing\Data\Command\Transaction;


use PapaLocal\Billing\ValueObject\TransactionInterface;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class SaveSuccessfulTransaction.
 *
 * @package PapaLocal\Billing\Data\Command\Transaction
 */
class SaveSuccessfulTransaction
{
    /**
     * @var GuidInterface
     */
    private $guid;

    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * SaveSuccessfulTransaction constructor.
     *
     * @param GuidInterface        $guid
     * @param TransactionInterface $transaction
     */
    public function __construct(
        GuidInterface $guid,
        TransactionInterface $transaction
    )
    {
        $this->guid = $guid;
        $this->transaction = $transaction;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid->value();
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->transaction->getUserId();
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): string
    {
        return $this->transaction->getUserGuid()->value();
    }

    /**
     * @return int
     */
    public function getBillingProfileId(): int
    {
        return $this->transaction->getBillingProfileId();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->transaction->getDescription();
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->transaction->getAmount();
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->transaction->getMonth();
    }

    /**
     * @return mixed
     */
    public function getReferralId()
    {
        return $this->transaction->getReferralId();
    }

    /**
     * @return mixed
     */
    public function getPayMethodId()
    {
        return $this->transaction->getPayMethodId();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->transaction->getType()->getValue();
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->transaction->getBalance();
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction->getTransactionId();
    }

    /**
     * @return string
     */
    public function getTransactionTime(): string
    {
        return $this->transaction->getTransactionTime();
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->transaction->getTimeCreated();
    }
}