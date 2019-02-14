<?php

/**
 * Created by eWebify, LLC.
 * User: Yacouba Keita
 * Date: 11/23/17
 * Time: 10:27 AM
 */

namespace Test\Unit\Entity;


use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Referral;


/**
 * Class ReferralTest
 *
 * @package Test\Unit\Entity
 *
 * Unit tests for PapaLocal\Entity\Referral.
 */
class ReferralTest extends TestCase
{
    public function testCanInstantiate()
    {
        $this->markTestIncomplete();

        $referral = new Referral();
        $this->assertInstanceOf(Referral::class, $referral);
    }

    public function testCanGetSetId()
    {
        $this->markTestIncomplete();

        $id = 7;
        $referral = (new Referral())->setId($id);

        $this->assertTrue(is_int($referral->getId()));
        $this->assertEquals($id, $referral->getId());
    }

    public function testCanGetSetTitle()
    {
        $this->markTestIncomplete();

        $title = 'this is a title';
        $referral = (new Referral())->setAbout($title);

        $this->assertTrue(is_string($referral->getAbout()));
        $this->assertEquals($title, $referral->getAbout());
    }

    public function testCanGetSetDescription()
    {
        $this->markTestIncomplete();

        $text = 'this is the description';
        $referral = (new Referral())->setNotes($text);

        $this->assertTrue(is_string($referral->getNotes()));
        $this->assertEquals($text, $referral->getNotes());
    }

    public function testCanGetSetTimeCreated()
    {
        $this->markTestIncomplete();

        $timeCreated = '2017-10-23 13:56:38';
        $referral = (new Referral())->setTimeCreated($timeCreated);

        $this->assertTrue(is_string($referral->getTimeCreated()));
        $this->assertEquals($timeCreated, $referral->getTimeCreated());
    }

    public function testCanGetSetTimeUpdated()
    {
        $this->markTestIncomplete();

        $timeUpdated = '2017-10-23 13:56:38';
        $referral = (new Referral())->setTimeUpdated($timeUpdated);

        $this->assertTrue(is_string($referral->getTimeUpdated()));
        $this->assertEquals($timeUpdated, $referral->getTimeUpdated());
    }

    public function testEqualsReturnsTrueWhenReferralIsProperlyCreated()
    {
        $this->markTestIncomplete();

        $referralAbout = 'Description';
        $referralNotes = 'Comment';

        $referralMock = $this->getMockBuilder(Referral::class)
            ->setMethodsExcept(['equals', 'getAbout', 'setAbout', 'getNotes', 'setNotes'])
            ->getMock();

        $referralMock->setAbout($referralAbout)
            ->setNotes($referralNotes);

        $result = $referralMock->equals($referralMock);

        $this->assertTrue($result);
    }
}