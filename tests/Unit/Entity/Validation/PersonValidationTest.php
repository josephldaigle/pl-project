<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/14/17
 */

namespace Test\Unit\Entity\Validation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PapaLocal\Entity\Person;


/**
 * Class PersonValidationTest.
 *
 * @package Test\Unit\Entity\Validation
 */
class PersonValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccessOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setId(3)
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenFirstNameIsBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('First name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Last name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsNoErrorWhenAboutIsBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');

    }

    public function testValidateReturnsCorrectErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsCorrectErrorWhenTimeUpdatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.')
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: update
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccessOnUpdate()
    {
        //set up fixtures
        $person = (new Person())
            ->setId(3)
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnUpdate()
    {
        //set up fixtures
        $person = (new Person())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setAbout('A president i think.');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be present.', $errors[0]->getMessage(),'unexpected error message');
    }

    public function testValidateReturnsNoErrorWhenOnlyFirstNameIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $person = (new Person())
            ->setId(3)
            ->setFirstName('Alex');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertEmpty($errors);
    }

    public function testValidateReturnsNoErrorWhenOnlyLastNameIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $person = (new Person())
            ->setId(3)
            ->setLastName('Ferguson');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertEmpty($errors);
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnUpdate()
    {
        //set up fixtures
        $person = (new Person())
            ->setId(3)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnUpdate()
    {
        $person = (new Person())
            ->setId(3)
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($person, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }
}