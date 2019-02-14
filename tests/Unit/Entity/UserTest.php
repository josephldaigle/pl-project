<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/2/17
 * Time: 6:48 PM
 */


namespace Test\Unit\Entity;


use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Entity\User;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;


/**
 * Class UserTest.
 *
 * Unit tests for PapaLocal\Entity\User
 *
 * @package Test\Unit\Entity
 */
class UserTest extends TestCase
{
    /**
     * Test that a Person can be created
     */
    public function testCanCreateUser()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test that the persons id can be set and fetched.
     */
    public function testCanGetSetId()
    {
        $id = 1;
        $user = (new User())
            ->setId($id);

        $this->assertTrue(is_int($user->getId()), 'failed type test');
        $this->assertEquals($id, $user->getId(), 'failed value test');
    }

    public function testCanGetSetIsActive()
    {
        $user = (new User())
            ->setIsActive(true);

        $this->assertTrue(is_bool($user->getIsActive()), 'failed type test');
        $this->assertEquals(true, $user->getIsActive(), 'failed value test');
    }

    public function testCanGetSetPassword()
    {
        $password = 'thisisabadpasswordbutworksfortesting';
        $user = (new User())
            ->setPassword($password);

        $this->assertTrue(is_string($user->getPassword()), 'failed type test');
        $this->assertEquals($password, $user->getPassword(), 'failed value test');
    }

    public function testCanGetSetTimeZone()
    {
        $timeZone = 'America/NewYork';
        $user = (new User())
            ->setTimeZone($timeZone);

        $this->assertTrue(is_string($user->getTimeZone()), 'failed type test');
        $this->assertEquals($timeZone, $user->getTimeZone(), 'failed value test');
    }

    public function testCanGetSetTimeCreated()
    {
        $timeCreated = date('Y-m-d H:i:s');
        $user = (new User())
            ->setTimeCreated($timeCreated);

        $this->assertTrue(is_string($user->getTimeCreated()), 'failed type test');
        $this->assertEquals($timeCreated, $user->getTimeCreated(), 'failed value test');
    }

    public function testCanGetSetPerson()
    {
        $personMock = $this->createMock(Person::class);

        $user = (new User())
            ->setPerson($personMock);

        $this->assertInstanceOf(Person::class, $user->getPerson(), 'failed type test');
        $this->assertEquals($personMock, $user->getPerson(), 'failed value test');
    }
}