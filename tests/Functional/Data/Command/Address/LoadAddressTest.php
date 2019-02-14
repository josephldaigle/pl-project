<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/26/18
 * Time: 9:28 PM
 */


namespace Test\Functional\Data\Command\Address;


use PapaLocal\Data\Command\Address\LoadAddress;
use PapaLocal\Data\DataService;
use PapaLocal\Entity\Address;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * LoadAddressTest.
 *
 * @package Test\Functional\Data\Command\Address
 */
class LoadAddressTest extends WebDatabaseTestCase
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
    		'Address'
	    ]);

    	parent::setUp();

        $this->persistence = $this->diContainer->get('PapaLocal\Data\DataService');
    }

    public function testLoadAddressIsSuccessWhenOnlyIdIsSpecified()
    {
        // set up fixtures
        $addressArr = $this->getConnection()
            ->createQueryTable('addr', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setId(intval($addressArr['id']));

        // exercise SUT
        $cmd = new LoadAddress($address);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertInstanceOf(Address::class, $result, 'unexpected type');

        $this->assertObjectHasAttribute('id', $result, 'id not present');
        $this->assertSame($addressArr['id'], $result->getId(), 'unexpected id');

        $this->assertObjectHasAttribute('streetAddress', $result, 'streetAddress not present');
        $this->assertSame($addressArr['streetAddress'], $result->getStreetAddress(), 'unexpected streetAddress');

        $this->assertObjectHasAttribute('city', $result, 'city not present');
        $this->assertSame($addressArr['city'], $result->getCity(), 'unexpected city');

        $this->assertObjectHasAttribute('state', $result, 'state not present');
        $this->assertSame($addressArr['state'], $result->getState(), 'unexpected state');

        $this->assertObjectHasAttribute('postalCode', $result, 'postalCode not present');
        $this->assertSame($addressArr['postalCode'], $result->getPostalCode(), 'unexpected postalCode');

        $this->assertObjectHasAttribute('country', $result, 'country not present');
        $this->assertSame($addressArr['country'], $result->getCountry(), 'unexpected country');
    }

    public function testLoadAddressIsSuccessWhenIdNotSpecified()
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
        $cmd = new LoadAddress($address);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertInstanceOf(Address::class, $result);
        $this->assertObjectHasAttribute('id', $result, 'id not present');
        $this->assertSame($addressArr['id'], $result->getId(), 'unexpected id');
        $this->assertObjectHasAttribute('streetAddress', $result, 'streetAddress not present');
        $this->assertSame($address->getStreetAddress(), $result->getStreetAddress(), 'unexpected streetAddress');
        $this->assertObjectHasAttribute('city', $result, 'city not present');
        $this->assertSame($address->getCity(), $result->getCity(), 'unexpected city');
        $this->assertObjectHasAttribute('state', $result, 'state not present');
        $this->assertSame($address->getState(), $result->getState(), 'unexpected state');
        $this->assertObjectHasAttribute('postalCode', $result, 'postalCode not present');
        $this->assertSame($address->getPostalCode(), $result->getPostalCode(), 'unexpected postalCode');
        $this->assertObjectHasAttribute('country', $result, 'country not present');
        $this->assertSame($address->getCountry(), $result->getCountry(), 'unexpected country');
    }

    public function testLoadAddressReturnsEmptyAddressWhenNotFound()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('100 Anyroad')
            ->setCity('Hampton')
            ->setState('Virginia')
            ->setPostalCode('33554')
            ->setCountry('United States');

        // exercise SUT
        $cmd = new LoadAddress($address);
        $result = $this->persistence->execute($cmd);

        // make assertions
        $this->assertInstanceOf(Address::class, $result);
        $this->assertNull($result->getId());
        $this->assertNull($result->getStreetAddress());
        $this->assertNull($result->getCity());
        $this->assertNull($result->getState());
        $this->assertNull($result->getPostalCode());
        $this->assertNull($result->getCountry());
    }
}