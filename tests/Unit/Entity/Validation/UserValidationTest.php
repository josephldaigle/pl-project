<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/8/17
 * Time: 9:36 PM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserValidationTest.
 */
class UserValidationTest extends KernelTestCase
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
        $user = (new User())
            ->setUsername('yacouba@ewebify.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsCorrectErrorWhenIdNotBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsCorrectErrorWhenIsActiveNotBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setIsActive(false)
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('isActive must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsCorrectErrorWhenRolesNotBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setRoles('ROLE_USER')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Roles must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsCorrectErrorWhenUsernameIsBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));


        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Username cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider passwordBlackListProvider
     */
    public function testValidateReturnsCorrectErrorWhenPasswordIsInvalidOnCreate($password, $errorMessage, $errorCount)
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword($password)
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount($errorCount, $errors, 'validation errors exists');
        $this->assertSame($errorMessage, $errors[0]->getMessage(), 'expected message not found');
    }

    public function passwordBlackListProvider()
    {
        return [
            ['#password13', 'The password field must contain at least one uppercase letter.', 1],  // no uppercase
            ['@PASSWORD90', 'The password field must contain at least one lowercase letter.', 1],  // no lowercase
            ['2PASSword07', 'The password field must contain at least one special character.', 1], // no special char
            ['*(passWORD~', 'The password field must contain at least one number.', 1], // no number
            ['#PASS word2**', 'The password field must not contain spaces.', 1], // No white spaces

            /*This test contains 7 characters.*/
            ['#1paWO4', 'The password field must contain at least 8 and at most 128 characters.', 1], // 8 char minimum allowed

            /*This test contains 129 characters.*/
            [
                '#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t#$%^&*()_!1234567890ABCDEFGHIJabcdefghij1qW@3eR$5t1234567890ABCDEFGHIJabcdefghi',
                'The password field must contain at least 8 and at most 128 characters.',
                1
            ], // 128 char maximum allowed
        ];
    }

    /**
     * @dataProvider passwordWhiteListProvider
     */
    public function testValidateReturnsNoErrorsWhenPasswordIsValidOnCreate($password)
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword($password)
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertEmpty($errors);
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
    public function testValidateReturnsCorrectErrorWhenUsernameIsInvalidOnCreate($email)
    {
        //set up fixtures
        $user = (new User())
            ->setUsername($email)
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('The email address is invalid.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function emailBlackListProvider()
    {
        return [
            ['testcompany.com'],
            ['test@companycom'],
        ];
    }

    public function testValidateReturnsCorrectErrorWhenTimeZoneIsBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time zone cannot be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsCorrectErrorWhenTimeCreatedIsNotBlankOnCreate()
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: update
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorOnSuccessOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenIdIsNotBlankOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setUsername('test@example.com')
            ->setPassword('!@#123ABCabc')
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Id must be present.', $errors[0]->getMessage(),'unexpected error message');
    }

    public function testValidateReturnsNoErrorWhenOnlyUsernameIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setUsername('test@example.com');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertEmpty($errors);

    }

    public function testValidateReturnsNoErrorWhenOnlyPasswordIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setPassword('!@#123ABCabc');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertEmpty($errors);

    }

    public function testValidateReturnsNoErrorWhenOnlyTimezoneIsBeingUpdatedOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setTimeZone('America/New_York');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertEmpty($errors);

    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotBlankOnUpdate()
    {
        //set up fixtures
        $user = (new User())
            ->setId(3)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($user, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be blank.', $errors[0]->getMessage(),  'unexpected error message');

    }


}