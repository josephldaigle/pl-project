<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/28/17
 */

namespace Test\Unit\Data\DataMapper;

use PHPUnit\Framework\TestCase;

use PapaLocal\Data\DataMapper\DataMapperInterface;
use PapaLocal\Test\DataMapperDummy;
use PapaLocal\Data\DataMapper\MapperFactory;
use PapaLocal\Entity\EntityFactory;

/**
 * Class DataMapperFactoryTest
 */
class DataMapperFactoryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->entityFactoryMock = $this->createMock(EntityFactory::class);
    }

    public function testCanInstantiate()
    {
        //set up fixtures
        $mapperFactory = new MapperFactory();

        //make assertions
        $this->assertInstanceOf(MapperFactory::class, $mapperFactory, 'unexpected instance type.');
    }


    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testCreateThrowsExceptionWhenMapperNameDoesNotExist()
    {
        //set up fixtures
        $mapperFactory = new MapperFactory();

        //exercise SUT
        $mapperFactory->create($this->entityFactoryMock, 'badClassName');
    }


    public function testCreateReturnsRequestedMapperOnSuccess()
    {
        //set up fixtures
        $mapperFactory = new MapperFactory();

        //exercise SUT
        $result = $mapperFactory->create($this->entityFactoryMock, DataMapperDummy::class);

        //make assertions
        $this->assertInstanceOf(DataMapperInterface::class, $result, 'unexpected instance type');
    }
}