<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/23/17
 * Time: 7:41 AM
 */


namespace Test\Functional\Data\Command\Person;


use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\Person\CreatePersonAddress;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\AddressInterface;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * CreatePersonAddressTest.
 *
 * @package Test\Functional\Data\Command\Person
 */
class CreatePersonAddressTest extends WebDatabaseTestCase
{
    /**
     * @var DataService
     */
    private $persistence;

    /**
     * {@inheritdoc}
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

    public function testCanInstantiate()
    {
        // set up fixtures
        $addressMock = $this->createMock(AddressInterface::class);

        // exercise SUT
        $command = new CreatePersonAddress(1, $addressMock);

        // make assertions
        $this->assertInstanceOf(CreatePersonAddress::class, $command, 'unexpected type');
    }

    public function testCreatePersonAddressReturnsAddressOnSuccess()
    {
        // set up fixtures
        $begAddCount = $this->getConnection()->getRowCount('Address');
        $begPersAddCount = $this->getConnection()->getRowCount('R_PersonAddress');

        $address = (new Address())
            ->setStreetAddress('100 Anywhere Ln')
            ->setCity('Hampton')
            ->setState('New York')
            ->setPostalCode(32649)
            ->setCountry('USA')
            ->setType(AttrType::ADDRESS_MAILING);

        $personId = intval($this->getConnection()
            ->createQueryTable('id', 'SELECT MAX(id) as \'id\' FROM Person')
            ->getRow(0)['id']);

        // exercise SUT
        $command = new CreatePersonAddress($personId, $address);
        $this->persistence->execute($command);

        // make assertions
        $this->assertTableRowCount('Address', $begAddCount + 1, 'Address table not incremented');
        $this->assertTableRowCount('R_PersonAddress', $begPersAddCount + 1, 'R_PersonAddress not incremented');
    }
}