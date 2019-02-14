<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 8:42 PM
 */

namespace Test\Unit\Entity;

use PapaLocal\Test\TestDummy;
use PapaLocal\Test\TestDummyTwo;
use PHPUnit\Framework\TestCase;

/**
 * EntityTest.
 */
class EntityTest extends TestCase
{
    public function testToArrayIsSuccess()
    {
        //set up fixtures

        //create test data
        $data = array(
            'member' => 'testValue'
        );

        //mock the SUT
        $entityMock = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['toArray', 'setMember', 'getMember'])
            ->getMock();

        //configure SUT
        $entityMock->setMember($data['member']);

        //exercise SUT
        $result = $entityMock->toArray();

        //make assertions
        $this->assertTrue(is_array($result), 'result not array');
        $this->assertArrayHasKey('member', $result, 'result missing index');
        $this->assertSame($data, $result, 'value test');
    }

    public function testToArrayOmitsUninitializedMembers()
    {
        //set up fixtures

        //mock the SUT
        $entityMock = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['toArray', 'getMember'])
            ->getMock();

        //exercise SUT
        $result = $entityMock->toArray();

        //make assertions
        $this->assertTrue(is_array($result), 'result not array');
        $this->assertArrayNotHasKey('member', $result, 'array has unexpected key');
    }

    public function testEqualsReturnsTrueWhenSubjectAndParamAreDifferentTypeAndBothEmpty()
    {
        //set up fixtures

        //mock the SUT
        $entityMock = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['equals'])
            ->getMock();

        $comparator = new TestDummyTwo();

        //exercise SUT and make assertion
        $this->assertTrue($entityMock->equals($comparator));
    }
    
    public function testEqualsReturnsFalseWhenValuesNotExactlySame()
    {
        //set up fixtures

        //mock the SUT
        $entityMock = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['equals'])
            ->getMock();

        $comparatorMock = $this->getMockBuilder(TestDummyTwo::class)
            ->setMethodsExcept(['setMember', 'getMember'])
            ->getMock();

        $comparatorMock->setMember('someValue');

        //exercise SUT and make assertion
        $this->assertTrue($entityMock->equals($comparatorMock));
    }
    
    public function testEqualsReturnsTrueWhenParamIdentical()
    {
        //set up fixtures

        //mock the SUT
        $entityMock = $this->getMockBuilder(TestDummy::class)
            ->setMethodsExcept(['equals', 'setMember'])
            ->getMock();

        $entityMock->setMember('someValue');
        
        $comparatorMock = $this->getMockBuilder(TestDummyTwo::class)
            ->setMethodsExcept(['setMember', 'getMember'])
            ->getMock();

        $comparatorMock->setMember('someValue');

        //exercise SUT and make assertion
        $this->assertTrue($entityMock->equals($comparatorMock));
    }
}