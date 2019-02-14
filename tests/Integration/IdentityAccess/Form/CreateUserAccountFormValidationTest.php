<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/22/18
 * Time: 11:46 AM
 */

namespace Test\Integration\IdentityAccess\Form;


use PapaLocal\IdentityAccess\Form\CreateUserAccountForm;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class CreateUserAccountFormValidationTest
 *
 * @package Test\Integration\IdentityAccess\Form
 */
class CreateUserAccountFormValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateWithNoCompanyReturnsNoError()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateWithCompanyReturnsErrorsWhenCompanyNotValid()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(4, $errors, 'unexpected validation errors exists');
    }

    public function testValidateWithCompanyReturnsNoErrorWhenCompanyIsRequired()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('Test Company, LLC.')
            ->setCompanyEmailAddress('tcllc@papalocal.com')
            ->setCompanyPhoneNumber('2241028338')
            ->setCompanyAddress([
                'streetAddress' => '102 Some Rd.',
                'city' => 'Somewhere',
                'state' => 'GA',
                'postalCode' => 30303
            ]);


        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenPasswordsDoNotMatch()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1IsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The password and confirm password must be identical.', $errors[0]->getMessage(),  'unexpected error message');
    }


    /**
     * @dataProvider phoneBlackListProvider
     */
    public function testValidateReturnsErrorWhenPhoneNumberLengthIsNotValid($phoneNumber)
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', $phoneNumber, '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The phone number must be at exactly 10 digits long', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function phoneBlackListProvider()
    {
        return [
            ['123456789'],
            ['11234567890'],
        ];
    }

    /**
     * @dataProvider passwordBlackListProvider
     */
    public function testValidateReturnsCorrectErrorWhenPasswordIsInvalid($password, $errorMessage)
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', $password, $password));

        //exercise SUT
        $errors = $this->validator->validate($form);

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
    public function testValidateReturnsNoErrorsWhenPasswordIsValidOnCreate($password)
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', $password, $password));

        //exercise SUT
        $errors = $this->validator->validate($form);

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

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenUsernameIsInvalid($username, $errorMessage)
    {
        //set up fixtures
        $form = (new CreateUserAccountForm($username, 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

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

    public function testValidateReturnsErrorWhenFirstNameIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', '', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The first name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', '', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        //exercise SUT
        $errors = $this->validator->validate($form);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The last name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyNameIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('')
             ->setCompanyEmailAddress('tcllc@papalocal.com')
             ->setCompanyPhoneNumber('2241028338')
             ->setCompanyAddress([
                 'streetAddress' => '102 Some Rd.',
                 'city' => 'Somewhere',
                 'state' => 'GA',
                 'postalCode' => 30303
             ]);


        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyAddressIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('Test Company, LLC.')
             ->setCompanyEmailAddress('tcllc@papalocal.com')
             ->setCompanyPhoneNumber('2241028338')
             ->setCompanyAddress([]);


        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyEmailAddressIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('Test Company, LLC.')
             ->setCompanyEmailAddress('')
             ->setCompanyPhoneNumber('2241028338')
             ->setCompanyAddress([
                 'streetAddress' => '102 Some Rd.',
                 'city' => 'Somewhere',
                 'state' => 'GA',
                 'postalCode' => 30303
             ]);


        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company email address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyPhoneNumberIsBlank()
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('Test Company, LLC.')
             ->setCompanyEmailAddress('tcllc@papalocal.com')
             ->setCompanyPhoneNumber('')
             ->setCompanyAddress([
                 'streetAddress' => '102 Some Rd.',
                 'city' => 'Somewhere',
                 'state' => 'GA',
                 'postalCode' => 30303
             ]);


        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company phone number cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider companyEmailBlackListProvider
     */
    public function testValidateReturnsErrorWhenCompanyEmailAddressIsInvalid($email, $errorMessage)
    {
        //set up fixtures
        $form = (new CreateUserAccountForm('gtester@papalocal.com', 'Guy', 'Tester', '2345678812', '#1thisIsAmazing', '#1thisIsAmazing'));

        $form->setCompanyName('Test Company, LLC.')
             ->setCompanyEmailAddress($email)
             ->setCompanyPhoneNumber('2241028338')
             ->setCompanyAddress([
                 'streetAddress' => '102 Some Rd.',
                 'city' => 'Somewhere',
                 'state' => 'GA',
                 'postalCode' => 30303
             ]);

        //exercise SUT
        $errors = $this->validator->validate($form, null, 'userWithCompany');

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