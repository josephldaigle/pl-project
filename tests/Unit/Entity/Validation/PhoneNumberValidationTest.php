<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/6/17
 * Time: 8:17 AM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class PhoneNumberValidationTest extends KernelTestCase
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
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('4048573948')
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnCreate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setPhoneNumber('4048573948')
            ->setType('Personal');


        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, sprintf('Expected 1 error, %s returned.', count($errors)));
        $this->assertSame('Id must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTypeIsBlankOnCreate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('4048573948')
            ->setType('');


        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Type must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenPhoneNumberIsBlankOnCreate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('')
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The phone number must be only numbers.', $errors[0]->getMessage(),
            'unexpected error message');
    }

    /**
     * @dataProvider phoneNumberBlackListProvider
     */
    public function testValidateReturnsErrorWhenPhoneNumberIsInvalidOnCreate($phoneNumber, $errorMessage)
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber($phoneNumber)
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected number of validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function phoneNumberBlackListProvider()
    {
        return [
            ['123456789', 'The phone number must be at exactly 10 digits long.'],
            ['11234567890', 'The phone number must be at exactly 10 digits long.'],
            ['123456789a', 'The phone number must be only numbers.'],
        ];
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('4048573948')
            ->setType('Personal')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('4048573948')
            ->setType('Personal')
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('create'));

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
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setPhoneNumber('4048573948');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsCorrectErrorWhenIdNotPresentOnUpdate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setPhoneNumber('4048573948');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be present.', $errors[0]->getMessage(),'expected message not found');
    }

    public function testValidateReturnsNoErrorWhenOnlyPhoneNumberIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setPhoneNumber('4048573948');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertEmpty($errors);
    }

    /**
     * @dataProvider phoneNumberBlackListProvider
     */
    public function testValidateReturnsErrorWhenPhoneNumberIsInvalidOnUpdate($phoneNumber, $errorMessage)
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setPhoneNumber($phoneNumber);

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnUpdate()
    {
        //set up fixtures
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnUpdate()
    {
        $phoneNumber = (new PhoneNumber())
            ->setId(3)
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($phoneNumber, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }
}