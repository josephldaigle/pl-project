<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/21/19
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class TransactionInterface.
 *
 * @package PapaLocal\Billing\ValueObject
 */
interface TransactionInterface
{
    /**
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface;

    /**
     * @return mixed
     */
    public function getUserId();

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface;
    /**
     * @return int
     */
    public function getBillingProfileId(): int;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return float
     */
    public function getAmount(): float;
    /**
     * @return string
     */
    public function getMonth(): string;

    /**
     * @return mixed
     */
    public function getReferralId();

    /**
     * @return mixed
     */
    public function getPayMethodId();

    /**
     * @return TransactionType
     */
    public function getType(): TransactionType;

    /**
     * @return float
     */
    public function getBalance(): float;

    /**
     * @return mixed
     */
    public function getTransactionId();

    /**
     * @return string
     */
    public function getTransactionTime(): string;

    /**
     * @return string
     */
    public function getTimeCreated(): string;
}