<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/13/18
 */


namespace PapaLocal\Billing\Data;


use PapaLocal\Billing\ValueObject\TransactionInterface;
use PapaLocal\Billing\ValueObject\VOFactory;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\Exception\QueryExceptionCode;
use PapaLocal\Core\Data\Record;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Billing\MonthlyTransactionSummary;
use PapaLocal\Entity\Billing\PastYearTransactionSummary;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Entity\Billing\TransactionList;
use PapaLocal\Entity\Exception\QueryException;


/**
 * Class TransactionRepository.
 *
 * @package PapaLocal\Billing\Data
 */
class TransactionRepository extends AbstractRepository
{
    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * TransactionRepository constructor.
     *
     * @inheritDoc
     *
     * @param DataResourcePool       $dataResourcePool
     * @param VOFactory              $voFactory
     * @param GuidGeneratorInterface $guidFactory
     */
    public function __construct(
        DataResourcePool $dataResourcePool,
        VOFactory $voFactory,
        GuidGeneratorInterface $guidFactory
    )
    {
        parent::__construct($dataResourcePool);

        $this->voFactory = $voFactory;
        $this->guidFactory = $guidFactory;
    }


    /**
     * @deprecated use mysql bus
     *
     * Update a transaction that has been AUTHORIZED by Authorize.net.
     *
     * @param Transaction $transaction
     * @param Guid $transactionGuid
     * @return int
     */
    public function saveSuccessfulTransaction(Transaction $transaction, Guid $transactionGuid = null)
    {
        $row = array(
            'billingProfileId' => $transaction->getBillingProfileId(),
            'userId' => $transaction->getUserId(),
            'description' => $transaction->getDescription(),
            'amount' => $transaction->getAmount(),
            'type' => $transaction->getType(),
            'transactionId' => null,
            'referralId' => null,
            'payMethodId' => null,
        );


        $row['guid'] = (is_null($transactionGuid))
            ? $this->guidFactory->generate()->value()
            : $transactionGuid->value();

        $this->tableGateway->setTable('JournalSuccess');
        $result = $this->tableGateway->create($row);

        return $result;
    }

    /**
     * Update a transaction that has been DECLINED by Authorize.net.
     *
     * @param Transaction $transaction
     * @return int
     */
    public function saveFailedTransaction(Transaction $transaction)
    {
        $this->tableGateway->setTable('JournalFailed');
        $result = $this->tableGateway->create($this->serializer->normalize($transaction, 'array', array(
        	'attributes' => array(
        	    'guid',
        		'billingProfileId',
		        'userId',
		        'description',
		        'amount',
		        'type',
		        'transactionTime',
		        'timeCreated',
		        'transactionId',
		        'referralId',
		        'payMethod'
	        )
        )));
        return $result;
    }

	/**
	 * Load all of a user's transaction history.
	 *
	 * @param int $userId
	 * @return TransactionList
	 */
    public function loadUsersTransactions(int $userId)
    {
        $this->tableGateway->setTable('v_user_transaction');
        $txnRows = $this->tableGateway->findBy('userId', $userId);

        $transactionList = $this->serializer->denormalize(array(), TransactionList::class, 'array');

        foreach ($txnRows as $row) {
            $transactionList->add($this->serializer->denormalize($row, Transaction::class, 'array'));
        }

        return $transactionList;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return TransactionList
     */
    public function findByUserGuid(GuidInterface $userGuid): TransactionList
    {
        $this->tableGateway->setTable('v_user_transaction');
        $txnRows = $this->tableGateway->findBy('userGuid', $userGuid->value());

        $transactionList = $this->serializer->denormalize(array(), TransactionList::class, 'array');

        foreach ($txnRows as $row) {
            $record = new Record($row);
            $transaction = $this->voFactory->createTransactionFromRecord($record);
            $transactionList->add($transaction);
        }

        return $transactionList;
    }

    /**
     * @param GuidInterface $transactionGuid
     *
     * @return TransactionInterface
     * @throws QueryException
     */
    public function findByGuid(GuidInterface $transactionGuid): TransactionInterface
    {
        $this->tableGateway->setTable('v_user_transaction');
        $txnRows = $this->tableGateway->findByGuid($transactionGuid->value());

        if (count($txnRows) < 0) {
            throw new QueryException(sprintf('Unable to locate a transaction with guid %s.', $transactionGuid->value()), QueryExceptionCode::NOT_FOUND());
        }

        $record = new Record($txnRows[0]);
        $transaction = $this->voFactory->createTransactionFromRecord($record);

        return $transaction;
    }

	/**
	 * Loads a users monthly transaction summary detail for the previous twelve months.
	 *
	 * @param int $userId
	 *
	 * @return mixed
	 */
    public function loadPastYearMonthlySummaryList(int $userId)
    {
    	$this->tableGateway->setTable('v_user_balance_past_year');
    	$rows = $this->tableGateway->findBy('userId', $userId);

    	$summaryList = $this->serializer->denormalize(array(), PastYearTransactionSummary::class, 'array');

    	foreach($rows as $row) {
    		$summaryList->add($this->serializer->denormalize($row, MonthlyTransactionSummary::class, 'array'));
	    }

	    return $summaryList;
    }

    /**
     * @param Guid $transactionGuid
     * @return int
     */
    public function removeFailedTransaction(Guid $transactionGuid)
    {
        $this->tableGateway->setTable('JournalSuccess');
        $txnRow = $this->tableGateway->findBy('guid', $transactionGuid->value());
        $result = $this->tableGateway->delete($txnRow['id']);
        return $result;
    }
}