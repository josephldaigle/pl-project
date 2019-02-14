<?php

/**
 * Created by PhpStorm.
 * Date: 2/7/18
 * Time: 1:57 PM
 */

namespace Test\Unit\ValueObject\Validation;

use PapaLocal\ValueObject\Form\ForgotPassword;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RegisterUserValidationTest
 */
class ForgotPasswordValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateReturnsNoErrorWhenEmailAddressIsValid()
    {
        //set up fixtures
        $emailAddress = (new ForgotPassword())
            ->setUsername('guy@tester.com');

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenEmailAddressIsBlank()
    {
        //set up fixtures
        $emailAddress = (new ForgotPassword())
            ->setUsername('');

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The email address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider companyEmailBlackListProvider
     */
    public function testValidateReturnsErrorEmailAddressIsInvalid($email, $errorMessage)
    {
        //set up fixtures
        $emailAddress = (new ForgotPassword())
            ->setUsername($email);

        //exercise SUT
        $errors = $this->validator->validate($emailAddress);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function companyEmailBlackListProvider()
    {
        return [
            ['testcompany.com', 'The email address provided is not a valid email.'],
            ['test@companycom', 'The email address provided is not a valid email.'],
        ];
    }

}