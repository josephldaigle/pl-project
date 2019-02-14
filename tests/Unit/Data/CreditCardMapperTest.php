<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/3/18
 * Time: 1:42 PM
 */

namespace Test\Unit\Data\DataMapper;

use PapaLocal\Data\DataMapper\CreditCardMapper;
use PapaLocal\Data\DataMapper\DataMapperInterface;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Test\TestDummy;
use PHPUnit\Framework\TestCase;

class CreditCardMapperTest extends TestCase
{
    public function testCanInstantiate()
    {
        $mapper = new CreditCardMapper();
        $this->assertInstanceOf(DataMapperInterface::class, $mapper);
    }
        //TODO: Rewrite these commented tests
//    /**
//     * @expectedException PapaLocal\Entity\Exception\UnhandledRequestException
//     * @expectedExceptionMessageRegExp /^(End of mapper chain reached)/
//     */
//    public function testMapToEntityThrowsExceptionWhenEntityRequestedNotCreditCard()
//    {
//        //set up fixtures
//        $tableRow = array(
//            'id' => 4,
//            'firstName' => 'Guy',
//            'lastName' => 'Tester',
//            'customerId' => 1234561234,
//            'accountNumber' => 1234123412341234,
//            'expirationDate' => 1222
//        );
//
//        //create mapper mock
//        $mapperMock = $this->getMockBuilder(CreditCardMapper::class)
//            ->setMethodsExcept(['mapToEntity', 'toEntity'])
//            ->getMock();
//
//        //exercise SUT
//        $mapperMock->mapToEntity(TestDummy::class, $tableRow);
//    }

//    public function testMapToEntityRemovesCorrectKeysWhenParamIsUserInstance()
//    {
//        //set up fixtures
//        $tableRow = array(
//            'id' => 4,
//            'isActive' => true,
//            'password' => 'thisismypassword',
//            'timeZone' => 'America/New_York',
//            'timeCreated' => date('Y-m-d H:i:s'),
//            'personId' => 1
//        );
//
//        //set up entity array
//        $entityArr = $tableRow;
//        unset($entityArr['personId']);
//
//        //create an entity stub to use as return value from EntityFactory
//        $entityStub = $this->getMockBuilder(User::class)
//            ->setMethods(['toArray'])
//            ->getMock();
//
//        $entityStub
//            ->method('toArray')
//            ->willReturn($entityArr);
//
//        //create entity factory mock
//        $entityFactoryMock = $this->getMockBuilder(EntityFactory::class)
//            ->setMethods(['createFromArray'])
//            ->getMock();
//
//        //assert entity factory method is called
//        $entityFactoryMock->expects($this->once())
//            ->method('createFromArray')
//            ->with($this->identicalTo(User::class ), $this->identicalTo($entityArr))
//            ->willReturn($entityStub);
//
//        //create mapper mock
//        $mapperMock = $this->getMockBuilder(UserMapper::class)
//            ->setConstructorArgs([$entityFactoryMock])
//            ->setMethodsExcept(['mapToEntity', 'toEntity'])
//            ->getMock();
//
//        //exercise SUT
//        $result = $mapperMock->mapToEntity(User::class, $tableRow);
//
//        //make assertions
//        $this->assertInstanceOf(User::class, $result, 'unexpected instance type');
//        $this->assertSame($entityArr, $result->toArray(), 'unexpected result value');
//    }

    /**
     * @expectedException PapaLocal\Entity\Exception\UnhandledRequestException
     * @expectedExceptionMessageRegExp /^(End of mapper chain reached)/
     */
    public function testMapToTableThrowsExceptionWhenParamIsNotInstanceOfCreditCard()
    {
        //set up fixtures
        $dummyStub = $this->createMock(TestDummy::class);

        //create mapper mock
        $mapperMock = $this->getMockBuilder(CreditCardMapper::class)
            ->setMethodsExcept(['toTable'])
            ->getMock();

        //exercise SUT
        $mapperMock->mapToTable($dummyStub);

    }
}