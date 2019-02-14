<?php
/**
 * Created by PhpStorm.
 * Date: 2/7/18
 * Time: 1:57 PM
 */


namespace Test\Integration\ReferralAgreement\Form;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\ReferralAgreement\Form\ReferralAgreementInviteeForm;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class ReferralAgreementInviteeFormValidationTest.
 *
 * @package Test\Integration\ReferralAgreement\Form
 */
class ReferralAgreementInviteeFormValidationTest extends KernelTestCase
{
    /**
     * @var Guid
     */
    private $guid;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        //boot Symfony kernel (app) for access to services
        self::bootKernel();

        //fetch validator service
        $this->validator = static::$kernel->getContainer()->get('validator');

        // instantiate guid
        $this->guid = $this->getMockBuilder(Guid::class)
            ->disableOriginalConstructor()
            ->setMethods(['value'])
            ->getMock();

        $this->guid->method('value')
            ->willReturn('70472dbe-4619-4382-9383-5e43595df867');
    }

    /**
     |----------------------------------------
     | Data Providers
     |----------------------------------------
     */
    public function phoneBlackListProvider()
    {
        return [
            'too short' => ['123456789', 1, 'The phone number must be exactly 10 digits long.'],
            'too long' => ['11234567890', 1, 'The phone number must be exactly 10 digits long.'],
            'not numeric' => ['abcdefghij', 2,'The phone number can only contain numbers.'],
            'begins with zero' => ['0123456789', 1, 'The phone number cannot begin with zero.']
        ];
    }

    public function emailBlackListProvider()
    {
        return [
            'empty string' => ['', 1, 'The email address cannot be blank.'],
            'no domain indicator (@)' => ['testcompany.com', 1, 'The email address is invalid.'],
            'missing dot (.) in extension' => ['test@companycom', 1, 'The email address is invalid.'],
            'too long' => ['somemeailaddressthatis2long@etest.com', 1, 'The email address cannot be longer than 36 characters.']
        ];
    }

    public function testValidateReturnsNoErrorWhenInviteFormIsValid()
    {
        //set up fixtures
        $emailAddress = new EmailAddress('guy@tester.com', EmailAddressType::PERSONAL());

        $invitee = new ReferralAgreementInviteeForm($this->guid, 'Guy', 'Tester', 'Description', $emailAddress);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenFirstNameIsNotPresent()
    {
        //set up fixtures
        $emailAddress = new EmailAddress('guy@tester.com', EmailAddressType::PERSONAL());

        $phoneNumberMock = $this->createMock(PhoneNumber::class);
        $phoneNumberMock->method('getPhoneNumber')
            ->willReturn('2346788978');

        $invitee = new ReferralAgreementInviteeForm($this->guid, '', 'Tester', 'Description', $emailAddress, $phoneNumberMock);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Please provide the recipient first name.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsNotPresent()
    {
        //set up fixtures
        $emailAddress = new EmailAddress('guy@tester.com', EmailAddressType::PERSONAL());

        $invitee = new ReferralAgreementInviteeForm($this->guid, 'Guy', '', 'Description', $emailAddress);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Please provide the recipient last name.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenMessageIsNotPresent()
    {
        //set up fixtures
        $emailAddress = new EmailAddress('guy@tester.com', EmailAddressType::PERSONAL());

        $invitee = new ReferralAgreementInviteeForm($this->guid, 'Guy', 'Tester', '', $emailAddress);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Please provide a brief message.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider phoneBlackListProvider
     */
    public function testValidateReturnsErrorWhenPhoneNumberIsNotInvalid($phoneNumber, $expectedErrCount, $expectedMessage)
    {
        //set up fixtures
        $emailAddress = new EmailAddress('guy@tester.com', EmailAddressType::PERSONAL());

        $phoneNumber = new PhoneNumber($phoneNumber, PhoneNumberType::PERSONAL());

        $invitee = new ReferralAgreementInviteeForm($this->guid, 'Guy', 'Tester', 'Description', $emailAddress, $phoneNumber);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount($expectedErrCount, $errors, 'unexpected validation errors exists');
        $this->assertSame($expectedMessage, $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider emailBlackListProvider
     *
     * @param $emailAddress
     * @param $expectedErrCount
     * @param $expectedMessage
     */
    public function testValidateReturnsErrorWhenEmailAddressIsNotInvalid($emailAddress, $expectedErrCount, $expectedMessage)
    {
        //set up fixtures
        $emailAddress = new EmailAddress($emailAddress, EmailAddressType::PERSONAL());

        $phoneNumber = new PhoneNumber('5552228888', PhoneNumberType::PERSONAL());

        $invitee = new ReferralAgreementInviteeForm($this->guid, 'Guy', 'Tester', 'Description', $emailAddress, $phoneNumber);

        //exercise SUT
        $errors = $this->validator->validate($invitee);

        //assertions
        $this->assertCount($expectedErrCount, $errors, 'unexpected validation errors exists');
        $this->assertSame($expectedMessage, $errors[0]->getMessage(),  'unexpected error message');
    }
}