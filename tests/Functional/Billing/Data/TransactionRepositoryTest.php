<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/14/18
 * Time: 10:15 PM
 */


namespace Test\Functional\Billing\Data;


use PapaLocal\Billing\Data\TransactionRepository;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Billing\MonthlyTransactionSummary;
use PapaLocal\Entity\Billing\PastYearTransactionSummary;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Entity\Billing\TransactionList;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * TransactionRepositoryTest.
 *
 * @package Test\Functional\Billing\Data
 */
class TransactionRepositoryTest extends WebDatabaseTestCase
{
	/**
	 * @var TransactionRepository
	 */
	private $transactionRepository;

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$this->configureDataSet([]);

		parent::setUp();

		$this->transactionRepository = $this->diContainer->get('PapaLocal\Billing\Data\TransactionRepository');
	}

	public function testSaveSuccessfulTransactionReturnsRowIdOnSuccess()
	{
		// set up fixtures
		$billProfRow = $this->getConnection()
			->createQueryTable('billing_profile',
				'SELECT * FROM BillingProfile WHERE isActive = 1 LIMIT 1')
			->getRow(0);

		$transaction = (new Transaction())
			->setAmount(35.00)
			->setType(Transaction::TYPE_DEBIT)
			->setDescription(Transaction::DESC_REFERRAL )
			->setBillingProfileId($billProfRow['id'])
			->setUserId($billProfRow['userId']);

		$begJournalRowCount = $this->getConnection()->getRowCount('JournalSuccess');

		// exercise SUT
		$result = $this->transactionRepository->saveSuccessfulTransaction($transaction);

		// make assertions
		$this->assertTrue(is_int($result), 'unexpected type');
		$this->assertGreaterThan(1, $result, 'unexpected value');
		$this->assertTableRowCount('JournalSuccess', $begJournalRowCount + 1,
            'unexpected table row count: JournalSuccess');
	}

    public function testSaveFailedTransactionReturnsRowIdOnSuccess()
    {
        // set up fixtures
        $billProfRow = $this->getConnection()
            ->createQueryTable('billing_profile',
                'SELECT * FROM BillingProfile WHERE isActive = 1 LIMIT 1')
            ->getRow(0);

        $transaction = (new Transaction())
            ->setAmount(35.00)
            ->setType(Transaction::TYPE_DEBIT)
            ->setDescription(Transaction::DESC_REFERRAL)
            ->setBillingProfileId($billProfRow['id'])
            ->setUserId($billProfRow['userId']);

        $begJournalRowCount = $this->getConnection()->getRowCount('JournalSuccess');

        // exercise SUT
        $result = $this->transactionRepository->saveSuccessfulTransaction($transaction);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertGreaterThan(1, $result, 'unexpected value');
        $this->assertTableRowCount('JournalSuccess', $begJournalRowCount + 1,
            'unexpected table row count: JournalSuccess');
	}

    public function testLoadUserTransactionsReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM JournalSuccess) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $result = $this->transactionRepository->loadUsersTransactions($userId);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $result, 'unexpected type');
        $this->assertEquals(0, $result->count(), 'unexpected number of transactions');
	}

    public function testLoadUserTransactionsReturnsTransactionListOnSuccess()
    {
        // set up fixtures
        // get a userId for user who has transactions
        $userId = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT DISTINCT userId FROM JournalSuccess LIMIT 1')
            ->getRow(0)['userId'];

        // load the user's transactions
        $usrTxns = $this->getConnection()
            ->createQueryTable('user_txn', 'SELECT * FROM JournalSuccess WHERE userId = '. $userId);

        // exercise SUT
        $transactions = $this->transactionRepository->loadUsersTransactions($userId);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $transactions, 'unexpected type');
        // TODO: Compare result to $usrTxns for validation of operation
	}

    public function testFindByUserGuidReturnsTransactionListOnSuccess()
    {
        // set up fixtures
        // get a userGuid for user who has transactions
        $userGuid = $this->getConnection()
            ->createQueryTable('user_guid', 'SELECT DISTINCT userGuid FROM v_user_transaction LIMIT 1')
            ->getRow(0)['userGuid'];

        // load the user's transactions
        $usrTxns = $this->getConnection()
            ->createQueryTable('user_txn', 'SELECT * FROM JournalSuccess WHERE userGuid = '. $userGuid);


        // exercise SUT
        $guid = new Guid($userGuid);
        $transactions = $this->transactionRepository->findByUserGuid($guid);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $transactions, 'unexpected type');
        // TODO: Compare result to $usrTxns for validation of operation
	}

    public function testFindByGuidReturnsTransactionOnSuccess()
    {
        // set up fixtures
        $txnGuid = $this->getConnection()
            ->createQueryTable('txn_guid', 'SELECT guid FROM v_user_transaction LIMIT 1')
            ->getRow(0)['guid'];

        // exercise SUT
        $guid = new Guid($txnGuid);

        // make assertions
        $transaction = $this->transactionRepository->findByGuid($guid);

        // exercise SUT
        $this->assertInstanceOf(\PapaLocal\Billing\ValueObject\Transaction::class, $transaction);
	}

    public function testLoadPastYearMonthlySummaryListReturnsCorrectResultWhenUserHasNoTransactions()
	{
		// set up fixtures
		// user id for user without transactions
		$userId = $this->getConnection()
			->createQueryTable('user_id', 'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM JournalSuccess) LIMIT 1')
			->getRow(0)['id'];

		// exercise SUT
		$result = $this->transactionRepository->loadPastYearMonthlySummaryList($userId);

		// make assertions
		$this->assertInstanceOf(PastYearTransactionSummary::class, $result, 'unexpected type');
		$this->assertEquals(12, $result->count(), 'unexpected num of records in collection');

		foreach($result->all() as $summary) {
			$this->assertInstanceOf(MonthlyTransactionSummary::class, $summary, 'unexpected type');
			$this->assertEquals(0.00, $summary->getBegBalance(), 'unexpected beginning balance');
			$this->assertEquals(0.00, $summary->getEndBalance(), 'unexpected ending balance');
			$this->assertEquals(0.00, $summary->getMonthTotal(), 'unexpected month total');
		}
	}
}