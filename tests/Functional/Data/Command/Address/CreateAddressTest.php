<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/18/18
 * Time: 6:20 PM
 */


namespace Test\Functional\Data\Command\Address;


use PapaLocal\Data\Command\Address\CreateAddress;
use PapaLocal\Entity\Address;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreateAddressTest.
 *
 * @package Test\Functional\Data\Command\Address
 */
class CreateAddressTest extends WebDatabaseTestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Address'
	    ]);

    	parent::setUp();


        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testCreateAddressReturnsIdOnSuccess()
    {
        $begRowCount = $this->getConnection()->getRowCount('Address');
        $address = (new Address())
            ->setStreetAddress('400 Winchester Ave')
            ->setCity('New Haven')
            ->setState('Connecticut')
            ->setPostalCode(30299)
            ->setCountry('UnitedStates');

        // exercise SUT
        $command = new CreateAddress($address);
        $this->persistence->setCommand($command);
        $result1 = $this->persistence->execute();

        // make assertions
        $this->assertTrue(is_int($result1), 'unexpected type');
        $this->assertTableRowCount('Address', $begRowCount + 1, 'table not incremented');
    }

    /**
     * @expectedException Doctrine\DBAL\DBALException
     * @expectedExceptionMessageRegExp /(Duplicate)/
     */
    public function testCreateAddressDoesNotSaveDuplicateAddresses()
    {
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('Address');

        $address = (new Address())
            ->setStreetAddress('400 Winchester Ave')
            ->setCity('New Haven')
            ->setState('Connecticut')
            ->setPostalCode(30299)
            ->setCountry('UnitedStates');

        // create first address
        $command = new CreateAddress($address);
        $this->persistence->setCommand($command);
        $result1 = $this->persistence->execute();

        // exercise SUT
        $this->assertTrue(is_int($result1), 'unexpected type');
        $this->assertTableRowCount('Address', $begRowCount + 1, 'table not incremented');

        // create second address
        $this->persistence->setCommand($command);
        $this->persistence->execute();
    }
}