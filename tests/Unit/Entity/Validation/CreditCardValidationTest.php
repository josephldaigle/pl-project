<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/30/17
 * Time: 1:42 PM
 */

namespace Test\Unit\Entity\Validation;

use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\CreditCard;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CompanyValidationTest.
 *
 * @package Test\Unit\Entity\Validation
 */
class CreditCardValidationTest extends KernelTestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @inheritdoc
     */
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

    public function acceptedCreditCardProvider()
    {
        return [
            ['370000000000002'],
            ['6011000000000012'],
            ['4007000000027'],
            ['4012888818888'],
            ['4111111111111111'],
            ['5424000000000015'],
            ['2223000010309703'],
            ['2223000010309711'],
        ];
    }

    public function rejectedCreditCardProvider()
    {
        return [
            ['3088000000000017'],
            ['38000000000006'],
        ];
    }

    public function validMonthRangeProvider()
    {
        $currentMonth = date('m');

        for($i = $currentMonth; $i <= 12; $i++) {

            return [
                [$i],
            ];
        }
    }

    public function invalidMonthRangeProvider()
    {
        return [
            ['0', 'Month must start at 1.'],
            ['13', 'Month cannot be more than 12.'],
        ];
    }

    public function validYearDigitsAmountProvider()
    {
        return [
            ['22'],
        ];
    }

    public function invalidYearDigitsAmountProvider()
    {
        return [
            [2, '1'],[1, '133']
        ];
    }

    public function validSecurityDigitsAmountProvider()
    {
        return [
            ['133'],['1334'],
        ];
    }

    public function invalidSecurityDigitsAmountProvider()
    {
        return [
            ['13', 'The security code must be at least 3 digits long.'],
            ['13334', 'The security code must be at most 4 digits long.'],
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
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenFirstNameIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('First name must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenLastNameIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Last name must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenCardSchemeIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Card number must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenCardSchemeIsInvalidOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('1111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Your credit card number is invalid.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenCardTypeIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Card type must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenCardExpirationMonthIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Expiration month must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider validMonthRangeProvider
     */
    public function testValidateReturnsNoErrorWhenCardExpirationMonthRangeIsValidOnCreate($range)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth($range)
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    /**
     * @dataProvider invalidMonthRangeProvider
     */
    public function testValidateReturnsErrorWhenCardExpirationMonthRangeIsInvalidOnCreate($range, $message)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth($range)
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($message, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenCardExpirationYearIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Expiration year must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider invalidYearDigitsAmountProvider
     */
    public function testValidateReturnsErrorWhenCardExpirationYearDigitsLengthIsInvalidOnCreate($errorCount, $digits)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear($digits)
            ->setSecurityCode('342')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount($errorCount, $errors, 'unexpected validation errors exists');
        $this->assertSame('The expiration year must be at exactly 2 digits long.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider validYearDigitsAmountProvider
     */
    public function testValidateReturnsNoErrorWhenCardExpirationYearDigitsLengthIsValidOnCreate($digits)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear($digits)
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenSecurityCodeIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('30')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Card security code must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider validSecurityDigitsAmountProvider
     */
    public function testValidateReturnsNoErrorWhenSecurityCodeDigitsAmountIsValidOnCreate($digits)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode($digits)
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    /**
     * @dataProvider invalidSecurityDigitsAmountProvider
     */
    public function testValidateReturnsErrorWhenSecurityCodeDigitsAmountIsInvalidOnCreate($digits, $message)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode($digits)
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame($message, $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAddressIsEmptyOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('234');

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Address must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /**
     * @dataProvider acceptedCreditCardProvider
     */
    public function testValidateReturnsNoErrorWhenCardIsValidOnCreate($creditCards)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber($creditCards)
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    /**
     * @dataProvider rejectedCreditCardProvider
     */
    public function testValidateReturnsErrorWhenCardIsInvalidOnCreate($creditCards)
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber($creditCards)
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('3424')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Your credit card number is invalid.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testGetMonthReturnsCurrentMonthOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('25')
            ->setSecurityCode('342')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testGetMonthReturnsErrorWhenCurrentMonthIsGreaterThenExpirationMonthInCurrentYearOnCreate()
    {
        // TODO: Rewrite test or fix validation logic
        $this->markTestIncomplete('Issue with testing the month trigger in the first month of the year.');

        $year = date('y');
        $month = date('m');
        if ($month == '1') {
            $month = 12;
            $year = $year - 1;
        }

        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth($month)
            ->setExpirationYear($year)
            ->setSecurityCode('342')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Expiration month must be greater or equal to current month.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testGetYearReturnsErrorWhenCurrentYearIsGreaterThenExpirationYearOnCreate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setFirstName('Thomas')
            ->setLastName('Jefferson')
            ->setCardNumber('4111111111111111')
            ->setCardType('visa')
            ->setExpirationMonth('12')
            ->setExpirationYear('17')
            ->setSecurityCode('342')
            ->setAddress(new Address());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Expiration year must be greater or equal to current year.', $errors[0]->getMessage(),  'unexpected error message');
    }
    /*
     |-----------------------------------
     | VALIDATION GROUP: update
     |-----------------------------------
    */

    public function testValidateReturnsErrorWhenCustomerIdIsEmptyOnUpdate()
    {
        //set up fixtures
        $creditCard= (new CreditCard());

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('update'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Customer ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsNoErrorWhenCustomerIdIsNotEmptyOnUpdate()
    {
        //set up fixtures
        $creditCard= (new CreditCard())
            ->setCustomerId(11233);

        //exercise SUT
        $errors = $this->validator->validate($creditCard, null, array('update'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

}