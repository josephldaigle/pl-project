<?php

/**
 * Created by PhpStorm.
 * Date: 2/7/18
 * Time: 1:57 PM
 */

namespace Test\Unit\ValueObject\Validation;

use PapaLocal\ValueObject\Form\ResetPassword;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RegisterUserValidationTest
 */
class ResetPasswordValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateReturnsNoErrorWhenUsernameIsValid()
    {
        //set up fixtures
        $emailAddress = (new ResetPassword())
            ->setUsername('guy@tester.com')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenUsernameIsBlank()
    {
        //set up fixtures
        $emailAddress = (new ResetPassword())
            ->setUsername('')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The email address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenPasswordsDoNotMatch()
    {
        //set up fixtures
        $company = (new ResetPassword())
            ->setUsername('guy@tester.com')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1Amazing');

        //exercise SUT
        $errors = $this->validator->validate($company, null, 'authenticated_change');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The password and confirm password must be identical.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenEmailAddressIsInvalid($email, $errorMessage)
    {
        //set up fixtures
        $emailAddress = (new ResetPassword())
            ->setUsername($email)
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function emailBlackListProvider()
    {
        return [
            ['testcompany.com', 'The email address provided is not a valid email.'],
            ['test@companycom', 'The email address provided is not a valid email.'],
        ];
    }


    /**
     * @dataProvider passwordBlackListProvider
     */
    public function testValidateReturnsCorrectErrorWhenPasswordIsInvalid($password, $errorMessage)
    {
        //set up fixtures
        $company = (new ResetPassword())
            ->setUsername('guy@tester.com')
            ->setPassword($password)
            ->setConfirmPassword($password);

        //exercise SUT
        $errors = $this->validator->validate($company, null, 'authenticated_change');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function passwordBlackListProvider()
    {
        return [
            ['#password13', 'The password field must contain at least one uppercase letter.'],  // no uppercase
            ['@PASSWORD90', 'The password field must contain at least one lowercase letter.'],  // no lowercase
            ['2PASSword07', 'The password field must contain at least one special character.'], // no special char
            ['*(passWORD~', 'The password field must contain at least one number.'], // no number
            ['#PASS word2**', 'The password field must not contain spaces.'], // No white spaces

            /*This test contains 7 characters.*/
            ['#1paWO4', 'The password field must contain at least 8 and at most 128 characters.'], // 8 char minimum allowed

            /*This test contains 129 characters.*/
            [
                '#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t1234567890ABCDEFGHIJabcdefghi',
                'The password field must contain at least 8 and at most 128 characters.'
            ], // 128 char maximum allowed
        ];
    }

    /**
     * @dataProvider passwordWhiteListProvider
     */
    public function testValidateReturnsNoErrorsWhenPasswordIsValid($password)
    {
        //set up fixtures
        $company = (new ResetPassword())
            ->setUsername('guy@tester.com')
            ->setPassword($password)
            ->setConfirmPassword($password);

        //exercise SUT
        $errors = $this->validator->validate($company);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function passwordWhiteListProvider()
    {
        return [
            ['!@#123ABCabc'],

            /*This test contains 128 characters.*/
            ['#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t1234567890ABCDEFGHIJabcdefgh'],

            /*This test contains 8 characters.*/
            ['#1paWO4;'],
        ];
    }

}