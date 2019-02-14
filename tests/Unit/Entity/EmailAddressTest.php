<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/21/17
 * Time: 6:59 PM
 */

namespace Test\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\EmailAddress;

/**
 * EmailAddressTest.
 */
class EmailAddressTest extends TestCase
{
    public function testCanCreateEmailAddress()
    {
        //exercise SUT
        $emailAddress = new EmailAddress();

        //make assertions
        $this->assertInstanceOf(EmailAddress::class, $emailAddress);
    }

    public function testCanGetSetId()
    {
        //set up fixtures
        $arg = 1;

        $emailAddressMock = $this->getMockBuilder(EmailAddress::class)
            ->setMethodsExcept(['setId', 'getId'])
            ->getMock();

        //exercise SUT
        $emailAddressMock->setId($arg);
        $resultProperty = $emailAddressMock->getId();

        //make assertions
        $this->assertTrue(is_int($resultProperty), 'failed type test');
        $this->assertEquals($arg, $resultProperty, 'failed value test');
    }

    public function testCanGetSetEmailAddress()
    {
        //set up fixtures
        $arg = 'test@example.com';

        $emailAddressMock = $this->getMockBuilder(EmailAddress::class)
            ->setMethodsExcept(['setEmailAddress', 'getEmailAddress'])
            ->getMock();

        //exercise SUT
        $emailAddressMock->setEmailAddress($arg);
        $resultProperty = $emailAddressMock->getEmailAddress();

        //make assertions
        $this->assertTrue(is_string($resultProperty), 'failed type test');
        $this->assertEquals($arg, $resultProperty, 'failed value test');
    }

    public function testCanGetSetTimeCreated()
    {
        //set up fixtures
        $arg = date('Y-m-d H:i:s');

        $emailAddressMock = $this->getMockBuilder(EmailAddress::class)
            ->setMethodsExcept(['setTimeCreated', 'getTimeCreated'])
            ->getMock();

        //exercise SUT
        $emailAddressMock->setTimeCreated($arg);
        $resultProperty = $emailAddressMock->getTimeCreated();

        //make assertions
        $this->assertTrue(is_string($resultProperty), 'failed type test');
        $this->assertEquals($arg, $resultProperty, 'failed value test');
    }

    public function testCanGetSetTimeUpdated()
    {
        //set up fixtures
        $arg = date('Y-m-d H:i:s');

        $emailAddressMock = $this->getMockBuilder(EmailAddress::class)
            ->setMethodsExcept(['setTimeUpdated', 'getTimeUpdated'])
            ->getMock();

        //exercise SUT
        $emailAddressMock->setTimeUpdated($arg);
        $resultProperty = $emailAddressMock->getTimeUpdated();

        //make assertions
        $this->assertTrue(is_string($resultProperty), 'failed type test');
        $this->assertEquals($arg, $resultProperty, 'failed value test');
    }
}