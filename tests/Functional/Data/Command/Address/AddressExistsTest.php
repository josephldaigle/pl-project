<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/19/18
 * Time: 9:55 PM
 */

namespace Test\Functional\Data\Command\Address;

use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Address\AddressExists;
use PapaLocal\Entity\Address;
use PapaLocal\Test\WebDatabaseTestCase;

/**
 * AddressExistsTest.
 */
class AddressExistsTest extends WebDatabaseTestCase
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

        // fetch data service
        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testAddressExistsReturnsAddressIdWhenMatchFound()
    {
        // set up fixtures
        $addressArr = $this->getConnection()
            ->createQueryTable('addr', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setStreetAddress($addressArr['streetAddress'])
            ->setCity($addressArr['city'])
            ->setState($addressArr['state'])
            ->setPostalCode($addressArr['postalCode'])
            ->setCountry($addressArr['country']);

        // exercise SUT
        $command = new AddressExists($address);
        $this->persistence->setCommand($command);

        $result = $this->persistence->execute();

        // make assertions
        $this->assertFalse(is_bool($result), 'unexpected type');
        $this->assertSame($addressArr['id'], $result, 'unexpected value');
    }

    public function testAddressExistsReturnsFalseWhenNoMatchFound()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('222 Not A St.')
            ->setCity('Someplace')
            ->setState('Arkansas')
            ->setPostalCode(12345)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_SHIPPING);

        // exercise SUT
        $command = new AddressExists($address);
        $this->persistence->setCommand($command);

        $result = $this->persistence->execute();

        // make assertions
        $this->assertEquals(false, $result);
    }

    public function testAddressExistsReturnsFalseOnSingleValueMismatch()
    {
        // set up fixtures
        $addressArr = $this->getConnection()
            ->createQueryTable('addr', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setStreetAddress($addressArr['streetAddress'])
            ->setCity('BadCity')       // value mismatch
            ->setState($addressArr['state'])
            ->setPostalCode($addressArr['postalCode'])
            ->setCountry($addressArr['country'])
            ->setType(AttrType::ADDRESS_SHIPPING);

        // exercise SUT
        $command = new AddressExists($address);
        $this->persistence->setCommand($command);

        $result = $this->persistence->execute();

        // make assertions
        $this->assertEquals(false, $result);
    }
}