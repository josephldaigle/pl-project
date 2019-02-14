<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/27/17
 * Time: 8:18 PM
 */


namespace Test\Functional\Data\Command\User\Billing;


use PapaLocal\Data\Command\User\Billing\CreateBillingProfile;
use PapaLocal\Data\DataService;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateBillingProfileTest.
 *
 * @package Test\Functional\Data\Command\User\Billing
 */
class CreateBillingProfileTest extends WebDatabaseTestCase
{
    /**
     * @var DataService
     */
    private $persistence;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Person',
		    'User',
		    'BillingProfile'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateBillingProfileReturnsProfileIdOnSuccess()
    {
        //set up fixtures
        $userId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM User')
            ->getRow(0)['id']);

        $profileMaxId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM BillingProfile')
            ->getRow(0)['id']);

        $customerId = 1234566789;

        //exercise SUT
        $command = new CreateBillingProfile($userId, $customerId);
        $this->persistence->setCommand($command);
        $result = $this->persistence->execute();

        //make assertions
        $this->assertTrue(is_int($result), 'unexpected result type');
        $this->assertEquals($profileMaxId + 1, $result, 'unexpected entry id');
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Duplicate entry)/
     */
    public function testCreateBillingProfileThrowsExceptionWhenAccountExists()
    {
        //set up fixtures
        $userId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM User')
            ->getRow(0)['id']);

        $profileMaxId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM BillingProfile')
            ->getRow(0)['id']);

        $customerId = 1234566789;

        $command = new CreateBillingProfile($userId, $customerId);
        $this->persistence->setCommand($command);
        $passOne = $this->persistence->execute();

        $this->assertTrue(is_int($passOne), 'unexpected result type');
        $this->assertEquals($profileMaxId + 1, $passOne, 'unexpected entry id');

        //exercise SUT
        $this->persistence->setCommand($command);
        $this->persistence->execute();
    }
}