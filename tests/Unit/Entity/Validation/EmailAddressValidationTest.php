<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/6/17
 * Time: 8:17 AM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\EmailAddress;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\EmailValidator;


class EmailAddressValidationTest extends KernelTestCase
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
     | DATA PROVIDER
     |-----------------------------------
    */
    public function emailBlackListProvider()
    {
        return [
            ['testcompany.com', 'The email address is invalid.'],
            ['test@companycom', 'The email address is invalid.'],
            ['test@company.commmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', 'Your email address cannot be longer than 36 characters.'],
        ];
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: create
     |-----------------------------------
    */
    public function testValidateReturnsNoErrorOnSuccessOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('Marvels@superboutique.com')
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setId(3)
            ->setEmailAddress('Marvels@superboutique.com')
            ->setType('Personal');


        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenTypeIsBlankOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('Marvels@superboutique.com')
            ->setType('');


        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenEmailAddressIsBlankOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('')
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Email cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenEmailAddressIsInvalidOnCreate($emailAddresses, $errorMessage)
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress($emailAddresses)
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('Marvels@superboutique.com')
            ->setType('Personal')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('Marvels@superboutique.com')
            ->setType('Personal')
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('create'));

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
        $email = (new EmailAddress())
            ->setId(3)
            ->setEmailAddress('Marvels@superboutique.com');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsCorrectErrorWhenIdNotPresentOnUpdate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress('Marvels@superboutique.com');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be present.', $errors[0]->getMessage(),'expected message not found');
    }

    public function testValidateReturnsNoErrorWhenOnlyEmailAddressIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setId(3)
            ->setEmailAddress('Marvels@superboutique.com');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertEmpty($errors);
    }

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenEmailAddressIsInvalidOnUpdate($emailAddresses, $errorMessage)
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setId(3)
            ->setEmailAddress($emailAddresses);

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnUpdate()
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setId(3)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    public function testValidateReturnsErrorWhenTimeUpdatedIsNotBlankOnUpdate()
    {
        $email = (new EmailAddress())
            ->setId(3)
            ->setTimeUpdated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time updated must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }

    /**
     * --------------------------------------------------
     * ON FORM SUBMIT
     * --------------------------------------------------
     */

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenEmailAddressIsInvalidOnFormSubmit($emailAddresses, $errorMessage)
    {
        //set up fixtures
        $email = (new EmailAddress())
            ->setEmailAddress($emailAddresses)
            ->setType('Personal');

        //exercise SUT
        $errors = $this->validator->validate($email, null, array('form_submit'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }
}