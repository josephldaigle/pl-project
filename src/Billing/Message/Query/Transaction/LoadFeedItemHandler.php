<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/20/19
 */


namespace PapaLocal\Billing\Message\Query\Transaction;


use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Billing\ValueObject\VOFactory;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Feed\Message\Query\LoadFeedItem;


/**
 * Class LoadFeedItemHandler.
 *
 * @package PapaLocal\Billing\Message\Query\Transaction
 */
class LoadFeedItemHandler
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
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * LoadFeedItemHandler constructor.
     *
     * @param TransactionRepository  $transactionRepository
     * @param VOFactory              $voFactory
     * @param GuidGeneratorInterface $guidFactory
     */
    public function __construct(TransactionRepository $transactionRepository, VOFactory $voFactory, GuidGeneratorInterface $guidFactory)
    {
        $this->transactionRepository = $transactionRepository;
        $this->voFactory = $voFactory;
        $this->guidFactory = $guidFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LoadFeedItem $query)
    {
        if ('transaction' !== $query->getType()) {
            return [];
        }

        $transaction = $this->transactionRepository->findByGuid($this->guidFactory->createFromString($query->getGuid()));

        return $this->voFactory->createTransactionFeedItem($transaction);
    }


}