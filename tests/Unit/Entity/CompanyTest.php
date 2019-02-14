<?php
/**
 * Created by eWebify, LLC.
 * User: Yacouba Keita
 * Date: 11/14/17
 * Time: 11:14 AM
 */

namespace Test\Unit\Entity;

use PapaLocal\Data\AttrType;
use PapaLocal\Entity\PhoneNumber;
use PhpCollection\Map;
use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Company;
use PapaLocal\Test\TestDummyTwo;

/**
 * Class CompanyTest
 */
class CompanyTest extends TestCase
{

    public function testCanCreateCompany()
    {
        $testCompany = new Company();
        $this->assertInstanceOf(Company::class, $testCompany);
    }

    public function testCanGetSetId()
    {
        $id = 24;
        $companyId = (new Company())->setId($id);
        $this->assertTrue(is_int($companyId->getId()),'failed type test');
        $this->assertEquals($id, $companyId->getId(), 'failed value test');
    }

    public function testCanGetSetName()
    {
        $name = 'Moonlight inc';
        $companyName = (new Company())->setName($name);
        $this->assertTrue(is_string($companyName->getName()),'failed type test');
        $this->assertEquals($name, $companyName->getName(), 'failed value test');
    }

    public function testCanGetSetAbout()
    {
        $about = 'Where we dance in the moonlight';
        $companyAbout = (new Company())->setAbout($about);
        $this->assertTrue(is_string($companyAbout->getAbout()),'failed type test');
        $this->assertEquals($about, $companyAbout->getAbout(), 'failed value test');
    }

    public function testCanGetSetDateFounded()
    {
        $dateFounded = 1992;
        $companyDateFounded = (new Company())->setDateFounded($dateFounded);
        $this->assertTrue(is_int($companyDateFounded->getDateFounded()),'failed type test');
        $this->assertEquals($dateFounded, $companyDateFounded->getDateFounded(), 'failed value test');
    }

    public function testCanGetSetTimeCreated()
    {
        $timeCreated = '2017-10-23 13:56:38';
        $companyTimeCreated= (new Company())->setTimeCreated($timeCreated);
        $this->assertTrue(is_string($companyTimeCreated->getTimeCreated()),'failed type test');
        $this->assertEquals($timeCreated, $companyTimeCreated->getTimeCreated(), 'failed value test');
    }

    public function testCanGetSetTimeUpdated()
    {
        $timeUpdated = '2017-12-14 13:56:54';
        $companyTimeUpdated= (new Company())->setTimeUpdated($timeUpdated);
        $this->assertTrue(is_string($companyTimeUpdated->getTimeUpdated()),'failed type test');
        $this->assertEquals($timeUpdated, $companyTimeUpdated->getTimeUpdated(), 'failed value test');
    }

//    public function testEqualsReturnsFalseWhenCompIsInvalidInstanceType()
//    {
//        //set up fixtures
//        $companyMock = $this->getMockBuilder(Company::class)
//            ->setMethodsExcept(['equals'])
//            ->getMock();
//
//        $comparator = new TestDummyTwo();
//
//        //exercise SUT
//        $result = $companyMock->equals($comparator);
//
//        //make assertions
//        $this->assertFalse($result);
//    }
//
//    public function testEqualsReturnsFalseWhenNamesDoNotMatch()
//    {
//        //set up fixtures
//        $companyMock = $this->getMockBuilder(Company::class)
//            ->setMethodsExcept(['equals', 'setName'])
//            ->getMock();
//        $companyMock->setName('Test Company Name');
//
//
//        $comparator = $this->createMock(Company::class);
//        $comparator->method('getName')
//            ->willReturn('Bad Company Name');
//
//        //exercise SUT
//        $result = $companyMock->equals($comparator);
//
//        //make assertions
//        $this->assertFalse($result);
//    }
//
//    public function testEqualsReturnsFalseWhenDateFoundedDoesNotMatch()
//    {
//        //set up fixtures
//        $companyMock = $this->getMockBuilder(Company::class)
//            ->setMethodsExcept(['equals', 'setName', 'setDateFounded'])
//            ->getMock();
//
//        $companyMock->setName('Test Company Name');
//        $companyMock->setDateFounded('2000');
//
//        $comparator = $this->createMock(Company::class);
//        $comparator->method('getName')
//            ->willReturn('Test Company Name');
//        $comparator->method('getDateFounded')
//            ->willReturn('1990');
//
//        //exercise SUT
//        $result = $companyMock->equals($comparator);
//
//        //make assertions
//        $this->assertFalse($result);
//    }

    public function testEqualsReturnTrueWhenCompanyIsProperlyGenerated()
    {
        //set up fixtures
        $companyName = 'TestCompanyName';
        $companyDateFounded = '2001';

        $companyMock = $this->getMockBuilder(Company::class)
            ->setMethodsExcept(['equals', 'setName', 'setDateFounded', 'getName', 'getDateFounded'])
            ->getMock();

        $companyMock->setName($companyName);
        $companyMock->setDateFounded($companyDateFounded);

        //exercise SUT
        $result = $companyMock->equals($companyMock);

        //make assertions
        $this->assertTrue($result);
    }

}