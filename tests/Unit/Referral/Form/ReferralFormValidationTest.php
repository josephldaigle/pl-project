<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 12/6/17
 * Time: 12:01 PM
 */

namespace Test\Unit\Referral\Form;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Referral\Form\ReferralForm;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;


class ReferralFormValidationTest extends KernelTestCase
{
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

    }

    public function testValidateReturnsErrorWhenRecipientIsNotPresent()
    {
        //set up fixtures
        $referral = new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            null,
            ''
        );

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');

    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: agreement
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorWithAgreementRecipientOnSuccess()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(new Guid('guid')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenAgreementRecipientGuidIsNull()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(null),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenFirstNameIsEmptyOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            '',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsEmptyOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            '',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenPhoneNumberIsInvalidOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            null,
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenEmailIsInvalidOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2234568756', PhoneNumberType::PERSONAL()),
            null,
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAddressIsEmptyOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2234568756', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            null,
            'Short description',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenDescriptionIsEmptyOnAgreement()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            '',
            new AgreementRecipient(new Guid('dc22ec5b-e5d2-42fe-ae23-13fcd01fd943')),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'agreement'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    /*
     |-----------------------------------
     | VALIDATION GROUP: contact
     |-----------------------------------
    */

    public function testValidateReturnsNoErrorWithContactOnSuccessContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsNoErrorWhenContactGuidIsEmptyOnSuccessContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                null
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenFirstNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            '',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            '',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenPhoneNumberIsInvalidOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            null,
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenEmailIsInvalidOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            null,
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAddressIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            null,
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenDescriptionIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            '',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientFirstNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                '',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));


        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientLastNameIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                '',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));


        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientPhoneNumberIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                null,
                new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }

    public function testValidateReturnsErrorWhenRecipientEmailAddressIsEmptyOnContact()
    {
        //set up fixtures
        $referral = (new ReferralForm(
            'Yacouba',
            'Keita',
            new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
            new EmailAddress('lgroom@papalocal.com', EmailAddressType::PERSONAL()),
            new Address('3245 ponce', 'atlanta', 'GA', '30303', 'USA'),
            'Short description',
            new ContactRecipient(
                'Yacouba',
                'Keita',
                new PhoneNumber('2298457898', PhoneNumberType::PERSONAL()),
                null,
                new Guid('guid')
            ),
            ''
        ));

        //exercise SUT
        $errors = $this->validator->validate($referral, null, array('Default', 'contact'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame(NotBlank::IS_BLANK_ERROR, $errors[0]->getCode(),'unexpected error message');
    }
}