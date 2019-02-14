<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/27/17
 * Time: 6:13 PM
 */

namespace Test\Integration\Data\DataMapper;

use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Data\DataMapper\MapperFactory;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Test\NonEntity;
use PapaLocal\Test\TestDummy;
use PHPUnit\Framework\TestCase;

/**
 * MapperTest.
 */
class MapperTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->mapperFactory = new MapperFactory();
        $this->entityFactory = new EntityFactory();
    }

    public function testCanInstantiate()
    {
        //set up fixtures

        //exercise SUT
        $mapper = new Mapper($this->mapperFactory, $this->entityFactory);

        //make assertions
        $this->assertInstanceOf(Mapper::class, $mapper);
    }

    /*
     |-------------------------------------------------------------------
     | Test that the Mapper correctly handles errors from EntityFactory.
     |-------------------------------------------------------------------
     */
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testMapToEntityThrowsExceptionWhenFactoryReceivesBadClass()
    {
        //set up fixtures
        $row = array('badColumn' => 'value');

        //exercise SUT
        $mapper = new Mapper($this->mapperFactory, $this->entityFactory);
        $mapper->mapToEntity('NonExistingClass', $row);
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\SetterNotFoundException
     * @expectedExceptionMessageRegExp /^(Index)(.)+(does not have a matching setter in class)/
     */
    public function testMapToEntityThrowsExceptionWhenFactoryReceivesBadColumn()
    {
        //set up fixtures
        $row = array('badColumn' => 'value');

        //exercise SUT
        $mapper = new Mapper($this->mapperFactory, $this->entityFactory);
        $mapper->mapToEntity(TestDummy::class, $row);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /(expects Param 1 to be an instance of PapaLocal\\Entity\\Entity)/
     */
    public function testMapToEntityThrowsExceptionWhenFactoryReceivesNonEntity()
    {
        //set up fixtures
        $row = array('badColumn' => 'value');

        //exercise SUT
        $mapper = new Mapper($this->mapperFactory, $this->entityFactory);
        $mapper->mapToEntity(NonEntity::class, $row);
    }

    public function testMapToEntityReturnsEntityOnSuccess()
    {
        //set up fixtures
        $entityArr = array('member' => 'value');

        //exercise SUT
        $mapper = new Mapper($this->mapperFactory, $this->entityFactory);
        $result = $mapper->mapToEntity(TestDummy::class, $entityArr);

        //make assertions
        $this->assertInstanceOf(Entity::class, $result);
        $this->assertSame($entityArr, $result->toArray());
    }

}