<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/18/18
 */

namespace Test\Unit\Entity\Billing;


use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Entity\Billing\TransactionList;
use PHPUnit\Framework\TestCase;

/**
 * Class TransactionListTest.
 *
 * @package Test\Unit\Entity\Billing
 */
class TransactionListTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /(End date cannot be before start date)/
     */
    public function testFindByDateThrowsExceptionWhenEndDatePrecedesStartDate()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $txnList->findByDate('2018-04-17', '2018-04-15');
    }

    public function testFindByDateReturnsCorrectlyFilteredList()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByDate('2018-04-17', '2018-04-18');

        // make assertions
        $this->assertEquals(7, $filtered->count(), 'unexpected count');

    }

    public function getTransactionsByDateFiltersCorrectlyWhenStartAndEndDateAreIdentical()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByDate('2018-04-17', '2018-04-17');

        // make assertions
        $this->assertEquals(7, $filtered->count(), 'unexpected count');
    }

    public function testFindByDateFiltersListCorrectlyWhenMultipleYears()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByDate('2017-04-17', '2018-04-18');

        // make assertions
        $this->assertEquals(9, $filtered->count(), 'unexpected count');
    }

    public function testFindByDateFiltersListCorrectlyWhenMultipleMonths()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByDate('2018-05', '2018-06');

        // make assertions
        $this->assertEquals(1, $filtered->count(), 'unexpected count');
    }

    public function testFindByUserIdReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByUserId(5);

        // make assertions
        $this->assertEquals(0, $filtered->count(), 'unexpected count');
    }

    public function testFindByUserIdFiltersListCorrectly()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByUserId(3);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(1, $filtered->count(), 'unexpected count');
    }

    public function testFindByAmountFiltersListCorrectly()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByAmount(30.00);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(3, $filtered->count(), 'unexpected count');
    }

    public function testFindByAmountReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->findByAmount(222.99);

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(0, $filtered->count(), 'unexpected count');
    }

    public function testGetAllDepositsReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();
        foreach (array_keys($txnList->all()) as $key) {
            if ($txnList->get($key)->getType() ==='credit' && $txnList->get($key)->getDescription() === 'Cash Deposit') {
                $txnList->remove($key);
            }
        }

        // exercise SUT
        $filtered = $txnList->getAllDeposits();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(0, $filtered->count(), 'unexpected count');
    }

    public function testGetAllDepositsFiltersListCorrectlyOnSuccess()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->getAllDeposits();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(2, $filtered->count(), 'unexpected count');
    }

    public function testGetAllWithdrawalsFiltersListCorrectlyOnSuccess()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->getAllWithdrawals();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(2, $filtered->count(), 'unexpected count');
    }

    public function testGetAllWithdrawalsReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        foreach (array_keys($txnList->all()) as $key) {
            if ($txnList->get($key)->getDescription() === 'Cash Withdrawal') {
                $txnList->remove($key);
            }
        }

        // exercise SUT
        $filtered = $txnList->getAllWithdrawals();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(0, $filtered->count(), 'unexpected count');
    }

    public function testGetAllChargesFiltersListCorrectlyOnSuccess()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        // exercise SUT
        $filtered = $txnList->getAllCharges();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(6, $filtered->count(), 'unexpected count');

    }

    public function testGetAllChargesReturnsEmptyListWhenNoneFound()
    {
        // set up fixtures
        $txnList = $this->getTransactionList();

        foreach (array_keys($txnList->all()) as $key) {
            if ($txnList->get($key)->getDescription() === 'Referral Purchase') {
                $txnList->remove($key);
            }
        }

        // exercise SUT
        $filtered = $txnList->getAllCharges();

        // make assertions
        $this->assertInstanceOf(TransactionList::class, $filtered, 'unexpected type');
        $this->assertEquals(0, $filtered->count(), 'unexpected count');
    }

    /**
     * Fetch a transaction list loaded with data.
     */
    private function getTransactionList()
    {
        $data = array(
            ['id' => 1, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Cash Deposit', 'type' => 'credit', 'amount' => 300.00, 'transactionTime' => '2018-04-17 10:54:14', 'timeCreated' => '2018-04-17 10:54:14', 'transactionId' => 123123123123],
            ['id' => 2, 'userId' => 3, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 25.00, 'transactionTime' => '2018-04-17 11:02:55', 'timeCreated' => '2018-04-17 11:02:55', 'transactionId' => 456451245746],
            ['id' => 3, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 30.00, 'transactionTime' => '2018-04-17 11:02:49', 'timeCreated' => '2018-04-17 11:02:49', 'transactionId' => 221221221221],
            ['id' => 4, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 30.00, 'transactionTime' => '2018-04-17 11:04:08', 'timeCreated' => '2018-04-17 11:04:08', 'transactionId' => 135491241112],
            ['id' => 5, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 30.00, 'transactionTime' => '2018-04-17 11:18:36', 'timeCreated' => '2018-04-17 11:18:36', 'transactionId' => 564874423185],
            ['id' => 6, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 20.00, 'transactionTime' => '2018-04-17 11:18:41', 'timeCreated' => '2018-04-17 11:18:41', 'transactionId' => 789995118554],
            ['id' => 7, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Cash Withdrawal', 'type' => 'debit', 'amount' => 100.00, 'transactionTime' => '2018-04-17 11:22:14', 'timeCreated' => '2018-04-17 11:22:17', 'transactionId' => 154881556448],
            ['id' => 8, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Cash Deposit', 'type' => 'credit', 'amount' => 100.00, 'transactionTime' => '2017-04-17 11:22:14', 'timeCreated' => '2017-04-17 11:22:17', 'transactionId' => 816465212549],
            ['id' => 9, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Referral Purchase', 'type' => 'debit', 'amount' => 20.00, 'transactionTime' => '2017-04-18 11:18:44', 'timeCreated' => '2017-04-18 11:17:32', 'transactionId' => 51086085409],
            ['id' => 10, 'userId' => 4, 'billingProfileId' => 1, 'description' => 'Cash Withdrawal', 'type' => 'debit', 'amount' => 100.00, 'transactionTime' => '2018-05-22 11:22:14', 'timeCreated' => '2018-05-22 11:22:17', 'transactionId' => 54687123549]
        );

        $txnList = new TransactionList();
        foreach($data as $row) {
            $transaction = (new Transaction())
                ->setId($row['id'])
                ->setUserId($row['userId'])
                ->setBillingProfileId($row['userId'])
                ->setDescription($row['description'])
                ->setAmount($row['amount'])
                ->setType($row['type'])
                ->setTimeFinalized($row['transactionTime'])
                ->setTimeCreated($row['timeCreated'])
                ->setANetTransId($row['transactionId']);

            $txnList->add($transaction);
        }

        return $txnList;
     }
}