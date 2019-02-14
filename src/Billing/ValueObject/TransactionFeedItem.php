<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/22/19
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Entity\FeedItemInterface;


/**
 * Class TransactionFeedItem.
 *
 * @package PapaLocal\Billing\ValueObject
 */
class TransactionFeedItem implements FeedItemInterface
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * TransactionFeedItem constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @inheritDoc
     */
    public function getGuid()
    {
        return $this->transaction->getGuid();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->transaction->getDescription();
    }

    /**
     * @inheritDoc
     */
    public function getTransactionType(): string
    {
        return $this->transaction->getType()->getValue();
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): string
    {
        return $this->transaction->getAmount();
    }

    /**
     * @inheritDoc
     */
    public function getAvailableBalance(): string
    {
        return $this->transaction->getBalance();
    }

    /**
     * @inheritDoc
     */
    public function getTimeCreated(): string
    {
        return $this->transaction->getTimeCreated();
    }

    /**
     * @inheritDoc
     */
    public function getTimeUpdated(): string
    {
        return ($this->transaction->getTransactionTime() > $this->transaction->getTimeCreated())
            ? $this->transaction->getTransactionTime()
            : $this->transaction->getTimeCreated();
    }

    /**
     * @inheritDoc
     */
    public function getFeedType(): string
    {
        return 'transaction';
    }

    /**
     * @inheritDoc
     */
    public function getCardBody(): string
    {
        return '';
    }
}