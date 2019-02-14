<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/26/18
 * Time: 8:54 PM
 */

namespace Test\Unit\Data\Command\Address;

use PapaLocal\Data\Command\Address\LoadAddress;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\AddressInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * LoadAddressTest.
 */
class LoadAddressTest extends TestCase
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
        $addresMock = $this->createMock(AddressInterface::class);

        // exercise SUT
        $cmd = new LoadAddress($addresMock);

        //make assertions
        $this->assertInstanceOf(LoadAddress::class, $cmd);
    }

    public function testLoadAddressReturnsAddressObjectOnSuccess()
    {
        // set up fixtures
        $addressMock = $this->createMock(AddressInterface::class);

        // set up table gateway expectations
        $this->tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->identicalTo('Address'))
            ->willReturn(null);
        $this->tableGatewayMock->expects($this->once())
            ->method('findByColumns')
            ->willReturn(array(array()));

        // set up serialier expectations
        $this->serializerMock->expects($this->once())
            ->method('normalize')
            ->willReturn(array());
        $this->serializerMock->expects($this->once())
            ->method('denormalize')
            ->willReturn(new Address());

        // exercise SUT
        $cmd = new LoadAddress($addressMock);
        $result = $cmd->execute($this->tableGatewayMock, $this->mapperMock, $this->serializerMock, $this->commandFactoryMock);

        //make assertions
        $this->assertInstanceOf(Address::class, $result);
    }
}