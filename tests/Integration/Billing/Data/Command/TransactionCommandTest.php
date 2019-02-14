<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 1/21/19
 */

namespace Test\Integration\Billing\Data\Command;


use PapaLocal\Billing\ValueObject\Transaction;
use PapaLocal\Billing\Data\MessageFactory;
use PapaLocal\Billing\ValueObject\TransactionType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class TransactionCommandTest.
 *
 * @package Test\Integration\Billing\Data\Command
 */
class TransactionCommandTest extends WebDatabaseTestCase
{
    /**
     * @var
     */
    private $dataBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        // configure test data set
        $this->configureDataSet([]);

        parent::setUp();

        // fetch services
        $this->dataBus    = $this->diContainer->get('messenger.bus.mysql');
        $this->msgFactory = $this->diContainer->get('PapaLocal\Billing\Data\MessageFactory');
    }

    public function testCanSaveSuccessfulTransaction()
    {
        $begTableRowCount = $this->getConnection()->getRowCount('JournalSuccess');
        $billProfRow = $this->getConnection()
            ->createQueryTable('bill_prof', 'SELECT * FROM v_user_billing_profile')
            ->getRow(0);

        $guid = new Guid('b25c66ae-15eb-4277-aa7d-7796ce2e4495');
        $userGuid = new Guid($billProfRow['userGuid']);
        $billingProfileId = $billProfRow['id'];
        $description = 'Referral Sale';
        $amount = 30.00;
        $type = TransactionType::DEBIT();

        $transaction = new Transaction(
            $guid,
            $userGuid,
            $billingProfileId,
            $description,
            $amount,
            $type
        );

        $saveTxnCmd = $this->msgFactory->newSaveSuccessfulTransaction($guid, $transaction);
        $this->dataBus->dispatch($saveTxnCmd);

        $this->assertTableRowCount('JournalSuccess', $begTableRowCount + 1, 'unexpected row count');
    }

}