<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 12:16 PM
 */

namespace Test\Unit\Entity;

use PapaLocal\Entity\Exception\SetterNotFoundException;
use PapaLocal\Test\NonEntity;
use PHPUnit\Framework\TestCase;
use PapaLocal\Test\TestDummy;
use PapaLocal\Entity\EntityFactory;

/**
 * EntityFactoryTest.
 */
class EntityFactoryTest extends TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testCreateThrowsExceptionWhenClassDoesNotExist()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create'])
            ->getMock();

        //exercise SUT
        $factoryMock->create('BadClassName');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /(expects Param 1)(.)+(to be an instance of)/
     */
    public function testCreateThrowsExceptionWhenRequestedClassNotInstanceOfEntity()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create'])
            ->getMock();

        //exercise SUT
        $factoryMock->create(NonEntity::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testCreateThrowsExceptionWhenParamIsEmpty()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create'])
            ->getMock();

        //exercise SUT
        $factoryMock->create('');
    }

    public function testCreateReturnsEntityOnSuccess()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create'])
            ->getMock();

        //exercise SUT
        $dummy = $factoryMock->create(TestDummy::class);

        //assert results
        $this->assertInstanceOf(TestDummy::class, $dummy);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testCreateFromArrayThrowsExceptionWhenClassDoesNotExist()
    {
        //set up fixtures
        $class = 'BadClassName';

        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $factoryMock->createFromArray($class, array());

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /(expects Param 1)(.)+(to be an instance of)/
     */
    public function testCreateFromArrayThrowsExceptionWhenRequestedClassNotInstanceOfEntity()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $factoryMock->createFromArray(NonEntity::class, array());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Unable to load class)/
     */
    public function testCreateFromArrayThrowsExceptionWhenParamOneIsEmpty()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $factoryMock->createFromArray('', array());
    }

    public function testCreateFromArrayThrowsExceptionWhenParamTwoIsEmpty()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $result = $factoryMock->createFromArray(TestDummy::class, array());

        //make assertions
        $this->assertInstanceOf(TestDummy::class, $result, 'invalid type');
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\SetterNotFoundException
     * @expectedExceptionMessageRegExp /^(Index)(.)+(does not have a matching setter)/
     */
    public function testCreateFromArrayThrowsExceptionWhenParamTwoContainsBadIndex()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $factoryMock->createFromArray(TestDummy::class, array('badIndex' => ''));
    }

    public function testCreateFromArrayIsSuccess()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $dummy = $factoryMock->createFromArray(TestDummy::class, array('member' => 'testvalue'));

        //assert results
        $this->assertInstanceOf(TestDummy::class, $dummy);
        $this->assertSame('testvalue', $dummy->getMember());
    }
}