<?php

/**
 * Created by PhpStorm.
 * Date: 2/7/18
 * Time: 1:57 PM
 */

namespace Test\Unit\ValueObject\Validation;

use PapaLocal\ValueObject\Form\RegisterUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @deprecated v1.0
 * Class RegisterUserValidationTest
 */
class RegisterUserValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateWithNoCompanyReturnsNoErrorWhenCompanyIsNotRequired()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateWithNoCompanyReturnsErrorWhenCompanyIsRequired()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

        //assertions
        $this->assertCount(4, $errors, 'unexpected validation errors exists');

    }

    public function testValidateWithCompanyReturnsNoErrorWhenCompanyIsRequired()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing')
            ->setCompanyName('Tester Company')
            ->setCompanyPhoneNumber('1234567890')
            ->setCompanyEmailAddress('company@tester.com')
            ->setCompanyAddress(array('234 oaks st', 'Atlanta', 'Georgia', '30303'));


        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenPasswordsDoNotMatch()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1Amazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The password and confirm password must be identical.', $errors[0]->getMessage(),  'unexpected error message');
    }


    /**
     * @dataProvider phoneBlackListProvider
     */
    public function testValidateReturnsErrorWhenPhoneNumberIsNotTenDigitsLong($phoneNumber)
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber($phoneNumber)
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

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
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('1234567890')
            ->setPassword($password)
            ->setConfirmPassword($password);

        //exercise SUT
        $errors = $this->validator->validate($company);

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
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('1234567890')
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

    /**
     * @dataProvider emailBlackListProvider
     */
    public function testValidateReturnsErrorWhenUsernameIsInvalid($username, $errorMessage)
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername($username)
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

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
        $company = (new RegisterUser())
            ->setFirstName('')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The first name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsBlank()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The last name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyNameIsBlank()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing')
            ->setCompanyName('')
            ->setCompanyPhoneNumber('1234567890')
            ->setCompanyEmailAddress('company@tester.com')
            ->setCompanyAddress(array('234 oaks st', 'Atlanta', 'Georgia', '30303'));


        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company name cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyAddressIsBlank()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing')
            ->setCompanyName('Tester Company')
            ->setCompanyPhoneNumber('1234567890')
            ->setCompanyEmailAddress('company@tester.com')
            ->setCompanyAddress(array());


        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyEmailAddressIsBlank()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing')
            ->setCompanyName('Tester Company')
            ->setCompanyPhoneNumber('1234567890')
            ->setCompanyEmailAddress('')
            ->setCompanyAddress(array('234 oaks st', 'Atlanta', 'Georgia', '30303'));


        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The company email address cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateWithCompanyReturnsErrorWhenCompanyPhoneNumberIsBlank()
    {
        //set up fixtures
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername('guy@tester.com')
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing')
            ->setCompanyName('Tester Company')
            ->setCompanyPhoneNumber('')
            ->setCompanyEmailAddress('Guy@tester.com')
            ->setCompanyAddress(array('234 oaks st', 'Atlanta', 'Georgia', '30303'));


        //exercise SUT
        $errors = $this->validator->validate($company, null, 'userWithCompany');

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
        $company = (new RegisterUser())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setUsername($email)
            ->setPhoneNumber('2346788978')
            ->setPassword('#1thisIsAmazing')
            ->setConfirmPassword('#1thisIsAmazing');

        //exercise SUT
        $errors = $this->validator->validate($company);

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