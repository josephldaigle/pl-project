<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 7:03 PM
 */

namespace Test\Unit\Data;

use PapaLocal\Test\TestDummyTwo;
use PHPUnit\Framework\TestCase;
use PapaLocal\Data\DataMapper\DataMapperInterface;
use PapaLocal\Data\DataMapper\EntityMapper;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Test\TestDummy;

/**
 * Class EntityMapperTest.
 */
class EntityMapperTest extends TestCase
{
    public function testCanInstantiate()
    {
        $mapper = new EntityMapper();
        $this->assertInstanceOf(DataMapperInterface::class, $mapper);
    }

    public function testMapToEntityCallsEntityFactory()
    {
        // set up fixtures

        // create method params for mapToEntity
        $paramOne = TestDummy::class;
        $paramTwo = array('member' => 'someValue');

        // create an entity stub to use as return value from EntityFactory
        $entityStub = $this->createMock(TestDummy::class);

        // create entity factory mock
        $entityFactoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethods(['createFromArray'])
            ->getMock();

        // assert entity factory method is called
        $entityFactoryMock->expects($this->once())
            ->method('createFromArray')
            ->with($this->identicalTo($paramOne), $this->identicalTo($paramTwo))
            ->willReturn($entityStub);

        // create mapper mock
        $mapperMock = $this->getMockBuilder(EntityMapper::class)
            ->setConstructorArgs([$entityFactoryMock])
            ->setMethodsExcept(['mapToEntity', 'toEntity'])
            ->getMock();

        // exercise SUT
        $result = $mapperMock->mapToEntity($paramOne, $paramTwo);

        // make assertions
        $this->assertInstanceOf(TestDummy::class, $result, 'unexpected type');
    }

    public function testMapToTableCallsToArrayOnEntitySupplied()
    {
        // set up fixtures
        $dummyArr = array('member' => 'someValue');

        // create an entity stub to use as return value from EntityFactory
        $entityStub = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['setMember'])
            ->getMock();
        $entityStub->setMember('testValue');
        $entityStub->expects($this->once())
            ->method('toArray')
            ->willReturn($dummyArr);

        // create mapper mock
        $mapperMock = $this->getMockBuilder(EntityMapper::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['mapToTable', 'toTable'])
            ->getMock();

        // exercise SUT
        $result = $mapperMock->mapToTable($entityStub);

        // make assertions
        $this->assertArrayHasKey('member', $result, '[member] key missing');
    }
}