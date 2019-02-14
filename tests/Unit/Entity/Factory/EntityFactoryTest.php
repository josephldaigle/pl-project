<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 12:16 PM
 */

use PHPUnit\Framework\TestCase;
use PapaLocal\Test\TestDummy;
use PapaLocal\Entity\Factory\EntityFactory;

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
     * @expectedExceptionMessageRegExp /(cannot be empty)/
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

    public function testCreateIsSuccess()
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
     * @expectedExceptionMessageRegExp /(Unable to load class)/
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
     * @expectedExceptionMessageRegExp /^(Param 1)(.)+(cannot be empty.)$/
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 2)(.)+(cannot be empty.)$/
     */
    public function testCreateFromArrayThrowsExceptionWhenParamTwoIsEmpty()
    {
        //set up fixtures
        $factoryMock = $this->getMockBuilder(EntityFactory::class)
            ->setMethodsExcept(['create', 'createFromArray'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        //exercise SUT
        $factoryMock->createFromArray(TestDummy::class, array());

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Index)(.)+(does not have a matching setter)(.)+$/
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