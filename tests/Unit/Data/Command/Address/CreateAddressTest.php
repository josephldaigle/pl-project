<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/23/18
 * Time: 1:22 PM
 */

namespace Test\Unit\Data\Command\Address;

use PapaLocal\Data\Command\Address\CreateAddress;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\AddressInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * CreateAddressTest.
 */
class CreateAddressTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        // set up fixtures
        $this->tableGatewayMock = $this->createMock(TableGateway::class);

        $this->mapperMock = $this->createMock(Mapper::class);

        $this->serializerMock = $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandFactoryMock = $this->createMock(CommandFactory::class);
    }

    public function testCanInstantiate()
    {
        // set up fixtures
        $addressMock = $this->createMock(AddressInterface::class);

        // exercise SUT
        $command = new CreateAddress($addressMock);

        // make assertions
        $this->assertInstanceOf(CreateAddress::class, $command);
    }

    public function testCreateAddressReturnsAddressWithIdOnSuccess()
    {
        // set up fixtures

        // create address mocks
        $addressMock = $this->createMock(AddressInterface::class);
        $addressArr = array(
            'streetAddress' => '100 Any Rd.',
            'city' => 'Macon',
            'state' => 'Georgia',
            'postalCode' => 12355,
            'country' => 'United States'
        );

        // set table gateway expectations
        $this->tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->identicalTo('Address'));

        $this->tableGatewayMock->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($addressArr))
            ->willReturn(1);

        // set serializer expectations
        $this->serializerMock->expects($this->once())
            ->method('normalize')
            ->willReturn($addressArr);

        // exercise SUT
        $command = new CreateAddress($addressMock);
        $result = $command->execute($this->tableGatewayMock, $this->mapperMock, $this->serializerMock, $this->commandFactoryMock);

        // make assertions
        $this->assertTrue(is_int($result));
        $this->assertEquals(1, $result);
    }
}