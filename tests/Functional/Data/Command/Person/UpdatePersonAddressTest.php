<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/7/18
 * Time: 9:54 AM
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\UpdatePersonAddress;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class UpdatePersonAddressTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class UpdatePersonAddressTest extends WebDatabaseTestCase
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
		    'Address',
		    'L_AddressType',
		    'R_PersonAddress'
	    ]);

        parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testUpdatePersonAddressCreatesAddressWhenNotExists() {
        // set up fixtures
        $begAddrTblCount = $this->getConnection()->getRowCount('Address');
        $begPersAddrTblCount = $this->getConnection()->getRowCount('R_PersonAddress');

        $address = (new Address())
            ->setStreetAddress('100 Mulberry Hill Ln.')
            ->setCity('Amityville')
            ->setState('Maryland')
            ->setPostalCode(30212)
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_PHYSICAL);

        // select person with address
        $personId = intval($this->getConnection()
            ->createQueryTable('pid', 'SELECT personId as \'id\' FROM R_PersonAddress WHERE typeId = 4')
            ->getRow(0)['id']);

        // exercise SUT
        $updateCmd = new UpdatePersonAddress($personId, $address);
        $result = $this->persistence->execute($updateCmd);

        // make assertions
        $this->assertTableRowCount('Address', $begAddrTblCount + 1,
            'Address table not incremented');
        $this->assertTableRowCount('R_PersonAddress', $begPersAddrTblCount,
            'unexpected row count for R_PersonAddress');

        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
    }

    public function testUpdatePersonAddressDoesNotCreateAddressWhenExists() {
        // set up fixtures
        $begAddrTblCount = $this->getConnection()->getRowCount('Address');
        $begPersAddrTblCount = $this->getConnection()->getRowCount('R_PersonAddress');

        $addressArr = $this->getConnection()
            ->createQueryTable('addr', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setStreetAddress($addressArr['streetAddress'])
            ->setCity($addressArr['city'])
            ->setState($addressArr['state'])
            ->setPostalCode($addressArr['postalCode'])
            ->setCountry($addressArr['country'])
            ->setType(AttrType::ADDRESS_PHYSICAL);

        $personId = intval($this->getConnection()
            ->createQueryTable('pid', 'SELECT personId as \'id\' FROM R_PersonAddress WHERE typeId = 4')
            ->getRow(0)['id']);

        // exercise SUT
        $updateCmd = new UpdatePersonAddress($personId, $address);
        $result = $this->persistence->execute($updateCmd);

        // make assertions
        $this->assertTableRowCount('Address', $begAddrTblCount,
            'Address table not incremented');
        $this->assertTableRowCount('R_PersonAddress', $begPersAddrTblCount,
            'unexpected row count for R_PersonAddress');

        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertSame(1, $result, 'unexpected value');
    }
}