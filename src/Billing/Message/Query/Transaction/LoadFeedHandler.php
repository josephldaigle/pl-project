<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/20/19
 */

namespace PapaLocal\Billing\Message\Query\Transaction;


use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Billing\ValueObject\VOFactory;
use PapaLocal\Feed\Message\Query\LoadFeed;


/**
 * Class LoadFeedHandler.
 *
 * @package PapaLocal\Billing\Message\Query\Transaction
 */
class LoadFeedHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * LoadFeedHandler constructor.
     *
     * @param TransactionRepository $transactionRepository
     * @param VOFactory             $voFactory
     */
    public function __construct(TransactionRepository $transactionRepository, VOFactory $voFactory)
    {
        $this->transactionRepository = $transactionRepository;
        $this->voFactory = $voFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LoadFeed $query)
    {
        if (! in_array('transaction', $query->getFeedType())) {
            return [];
        }

        // load all user notifications
        $transactions = $this->transactionRepository->findByUserGuid($query->getUser()->getGuid());

        // replace all elements with feed items
        foreach ($transactions as $key => $transaction) {
            $transactions->replace($this->voFactory->createTransactionFeedItem($transaction), $key);
        }

        return $transactions;
    }
}