<?php
/**
 * Created by eWebify, LLC.
 * Date: 10/24/17
 */

namespace Test\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\Person;

/**
 * Class PersonTest.
 *
 */
class PersonTest extends TestCase
{
    /**
     * Test that a Person can be created
     */
    public function testCanCreatePerson()
    {
        $person = new Person();
        $this->assertInstanceOf(Person::class, $person);
    }

    /**
     * Test that the persons id can be set and fetched.
     */
    public function testCanGetSetId()
    {
        $id = 1;
        $person = (new Person())
            ->setId($id);

        $this->assertTrue($id === $person->getId());
        $this->assertTrue(is_int($person->getId()));
    }

    /**
     * Test that the persons first name can be set and fetched.
     */
    public function testCanGetSetFirstName()
    {
        $firstName = 'Danny';
        $person = (new Person())
            ->setFirstName($firstName);

        $this->assertSame($firstName, $person->getFirstName());
        $this->assertTrue(is_string($person->getFirstName()));
    }

    /**
     * Test that a persons last name can be set and fetched.
     */
    public function testCanGetSetLastName()
    {
        $lastName = 'McBride';
        $person = (new Person())
            ->setLastName($lastName);

        $this->assertSame($lastName, $person->getLastName());
        $this->assertTrue(is_string($person->getLastName()));
    }

    /**
     * Test that a persons description can be set and fetched.
     */
    public function testCanGetSetAbout()
    {
        $about = 'This is a short description.';
        $person = (new Person())
            ->setAbout($about);

        $this->assertSame($about, $person->getAbout());
        $this->assertTrue(is_string($person->getAbout()));
    }

    /**
     * Test that a persons time created can be set and fetched.
     */
    public function testCanGetSetTimeCreated()
    {
        $timeCreated = date('Y-m-d H:i:s');
        $person = (new Person())
            ->setTimeCreated($timeCreated);

        $this->assertSame($timeCreated, $person->getTimeCreated());
        $this->assertTrue(is_string($person->getTimeCreated()));
    }

    /**
     * Test that a persons time created can be set and fetched.
     */
    public function testCanGetSetTimeUpdated()
    {
        $timeUpdated = date('Y-m-d H:i:s');
        $person = (new Person())
            ->setTimeUpdated($timeUpdated);

        $this->assertSame($timeUpdated, $person->getTimeUpdated());
        $this->assertTrue(is_string($person->getTimeUpdated()));
    }

    public function testToArrayReturnsEmptyArrayWhenPersonIsEmpty()
    {
        //create fixtures
        $person = new Person();

        //exercise SUT
        $result = $person->toArray();

        //assert result is empty array
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }

    public function testToArrayReturnsAllMembersFromObject()
    {
        //set up fixtures
        $arr = array(
            'firstName' => 'Paul',
            'lastName' => 'Petersen',
            'about' => 'A short bio about Paul.',
            'timeCreated' => date('Y-m-d H:i:s')
        );

        $person = (new Person())
            ->setFirstName($arr['firstName'])
            ->setLastName($arr['lastName'])
            ->setAbout($arr['about'])
            ->setTimeCreated($arr['timeCreated']);

        //exercise SUT
        $result = $person->toArray();

        //assert all members have values
        foreach ($arr as $key => $val) {
            $this->assertArrayHasKey($key, $result);
            $this->assertNotNull($result[$key]);
            $this->assertNotEmpty($result[$key]);
            $this->assertSame($val, $result[$key]);
        }
    }

    public function testToArrayReturnsOnlyInitializedMemberVars()
    {
        //set up fixtures
        $arr = array(
            'firstName' => 'Paul',
            'lastName' => 'Petersen',
        );

        $person = (new Person())
            ->setFirstName($arr['firstName'])
            ->setLastName($arr['lastName']);

        //exercise SUT
        $result = $person->toArray();

        //assert all members have values
        foreach ($arr as $key => $val) {
            $this->assertArrayHasKey($key, $result);
            $this->assertNotNull($result[$key]);
            $this->assertNotEmpty($result[$key]);
            $this->assertSame($val, $result[$key]);
        }
    }

}
