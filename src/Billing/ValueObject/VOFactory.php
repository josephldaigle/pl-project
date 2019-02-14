<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/21/19
 */


namespace PapaLocal\Billing\ValueObject;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Factory\VOFactory as BaseVoFactory;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;


/**
 * Class VOFactory.
 *
 * @package PapaLocal\Billing\ValueObject
 */
class VOFactory extends BaseVoFactory
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * VOFactory constructor.
     *
     * @param GuidGeneratorInterface $guidFactory
     */
    public function __construct(GuidGeneratorInterface $guidFactory)
    {
        $this->guidFactory = $guidFactory;
    }

    /**
     * Create an immutable Transaction object.
     *
     * @param string      $guid
     * @param string      $userGuid
     * @param int         $billingProfileId
     * @param string      $description
     * @param float       $amount
     * @param string      $month
     * @param string      $type
     * @param string      $transactionTime
     * @param string      $timeCreated
     * @param float       $balance
     * @param int|null    $userId
     * @param int|null    $payMethodId
     * @param string|null $transactionId
     * @param int|null    $referralId
     *
     * @return Transaction
     */
    public function createTransaction(
        string $guid,
        string $userGuid,
        int $billingProfileId,
        string $description,
        float $amount,
        string $month,
        string $type,
        string $transactionTime,
        string $timeCreated,
        float $balance = 0.00,
        int $userId = null,
        int $payMethodId = null,
        string $transactionId = '',
        int $referralId = null
    ): Transaction
    {
        return new Transaction(
            $this->guidFactory->createFromString($guid),
            $this->guidFactory->createFromString($userGuid),
            $billingProfileId,
            $description,
            $amount,
            TransactionType::{strtoupper($type)}(),
            $balance,
            $month,
            $transactionTime,
            $timeCreated,
            $userId,
            $payMethodId,
            $transactionId,
            $referralId
        );
    }

    /**
     * @param RecordInterface $record
     *
     * @return TransactionInterface
     */
    public function createTransactionFromRecord(RecordInterface $record): TransactionInterface
    {
        return $this->createTransaction(
            $record['guid'],
            $record['userGuid'],
            $record['billingProfileId'],
            $record['description'],
            $record['amount'],
            $record['month'],
            $record['type'],
            $record['transactionTime'],
            $record['timeCreated'],
            0.00,
            $record['userId'],
            (!isset($record['payMethodId']) ? null : $record['payMethodId']),
            (!isset($record['transactionId']) ? '' : $record['transactionId']),
            (! isset($record['referralId']) ? null : $record['referralId'])
        );
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return TransactionFeedItem
     */
    public function createTransactionFeedItem(TransactionInterface $transaction): TransactionFeedItem
    {
        return new TransactionFeedItem($transaction);
    }

}