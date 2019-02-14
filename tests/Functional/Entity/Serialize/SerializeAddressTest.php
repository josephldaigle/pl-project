<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/9/18
 * Time: 7:46 PM
 */


namespace Test\Functional\Entity\Serialize;


use PapaLocal\Data\AttrType;
use PapaLocal\Entity\Address;
use PapaLocal\Test\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SerializeAddressTest
 *
 * @package Test\Functional\Entity\Serialize
 */
class SerializeAddressTest extends WebTestCase
{
	/**
	 * @var SerializerInterface
	 */
    private $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        // set up fixtures
        $this->serializer = $this->diContainer->get('serializer');
    }

    /**
     * Simulates conversion of Address to array, prior to database save.
     */
    public function testNormalizeReturnsExpectedFieldsOnSuccess()
    {
        // set up fixtures
        $address = (new Address())
            ->setStreetAddress('1600 Pennsylvania Ave')
            ->setCity('Washington')
            ->setState('District of Columbia')
            ->setPostalCode('20001')
            ->setCountry('United States')
            ->setType(AttrType::ADDRESS_MAILING);

        // exercise SUT
        $result = $this->serializer->normalize($address, 'array', array(
            'attributes' => array(
                'streetAddress',
                'city',
                'state',
                'postalCode',
                'country'))
        );

        // make assertions
        $this->assertArrayNotHasKey('id', $result);
        $this->assertArrayNotHasKey('type', $result);
        $this->assertArrayHasKey('streetAddress', $result);
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('state', $result);
        $this->assertArrayHasKey('postalCode', $result);
        $this->assertArrayHasKey('country', $result);
    }

    public function testDenormalizeConvertsExpectedFieldsOnSuccess()
    {
        // set up fixtures
        $addressArr = array(
            'id' => 13,
            'streetAddress' => '100 Anytown',
            'city' => 'Frankfurter',
            'state' => 'Kentucky',
            'postalCode' => '35430',
            'country' => 'United States',
            'type' => AttrType::ADDRESS_MAILING
        );

        // exercise SUT
        $result = $this->serializer->denormalize($addressArr, Address::class, 'array');

        // make assertions
        $this->assertInstanceOf(Address::class, $result);

        // assert expected properties exist
        $this->assertObjectHasAttribute('id', $result);
        $this->assertObjectHasAttribute('streetAddress', $result);
        $this->assertObjectHasAttribute('city', $result);
        $this->assertObjectHasAttribute('state', $result);
        $this->assertObjectHasAttribute('postalCode', $result);
        $this->assertObjectHasAttribute('country', $result);
        $this->assertObjectHasAttribute('type', $result);

        // assert properties have expected values
        $this->assertSame($addressArr['id'], $result->getId());
        $this->assertSame($addressArr['streetAddress'], $result->getStreetAddress());
        $this->assertSame($addressArr['city'], $result->getCity());
        $this->assertSame($addressArr['state'], $result->getState());
        $this->assertSame($addressArr['postalCode'], $result->getPostalCode());
        $this->assertSame($addressArr['country'], $result->getCountry());
        $this->assertSame($addressArr['type'], $result->getType());
    }
}