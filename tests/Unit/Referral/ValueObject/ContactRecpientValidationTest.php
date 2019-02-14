<?php

/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/17/18
 * Time: 12:08 PM
 */

namespace Test\Unit\Referral\ValueObject;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;


class ContactRecpientValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateReturnsNoErrorOnSuccess()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Guid('guid')
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenRecipientFirstNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            '',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Guid('guid')
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientLastNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            'Yacouba',
            '',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Guid('guid')
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientPhoneNumberIsEmptyOnContact()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            'Yacouba',
            'Keita',
            null,
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Guid('guid')
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientEmailAddressIsEmptyOnContact()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            null,
            new Guid('guid')
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsNoErrorWhenContactGuidIsEmpty()
    {
        //set up fixtures
        $referral = new ContactRecipient(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            null
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }
}