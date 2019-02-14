<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 4/29/18
 * Time: 12:33 PM
 */

namespace Test\Unit\Entity\Validation;
use PapaLocal\Entity\Billing\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TransactionValidationTest extends KernelTestCase
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
    | VALIDATION GROUP: create
    |-----------------------------------
   */

    public function testValidateReturnsNoErrorOnSuccessOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(5)
            ->setDescription('Transaction description')
            ->setAmount(300)
            ->setType('Auto Recharge')
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenBillingProfileIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setUserId(5)
            ->setDescription('Transaction description')
            ->setAmount(300)
            ->setType('Auto Recharge')
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Billing profile ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenUserIdIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setDescription('Transaction description')
            ->setAmount(300)
            ->setType('Auto Recharge')
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('User ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenDescriptionIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(5)
            ->setAmount(300)
            ->setType('Auto Recharge')
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Description must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAmountIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(5)
            ->setDescription('Transaction description')
            ->setType('Auto Recharge')
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Transaction amount must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTypeIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(5)
            ->setDescription('Transaction description')
            ->setAmount(300)
            ->setANetTransId(4);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Transaction type must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAuthorizedNetTransactionIsNotPresentOnCreate()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(5)
            ->setDescription('Transaction description')
            ->setAmount(300)
            ->setType('Auto Recharge');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('create'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Authorize.net transaction ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    /*
    |-----------------------------------
    | VALIDATION GROUP: display
    |-----------------------------------
   */

    public function testValidateReturnsNoErrorOnSuccessOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(0, $errors, 'unexpected validation errors exists');
    }

    public function testValidateReturnsErrorWhenBillingProfileIdIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Billing profile ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenUserIdIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('User ID must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenDescriptionIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Description must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenAmountIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setType('Auto Recharge')
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Transaction amount must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTypeIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setBalance(800)
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Transaction type must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenBalanceIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setTimeCreated('2017-10-23 13:56:38');

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Transaction balance must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

    public function testValidateReturnsErrorWhenTimeCreatedIsNotPresentOnDisplay()
    {
        //set up fixtures
        $transaction = (new Transaction())
            ->setBillingProfileId(7)
            ->setUserId(3)
            ->setDescription('Transaction description')
            ->setAmount(500)
            ->setType('Auto Recharge')
            ->setBalance(800);

        //exercise SUT
        $errors = $this->validator->validate($transaction, null, array('display'));

        //assertions
        $this->assertCount(1, $errors, 'unexpected validation errors exists');
        $this->assertSame('Time created must be present.', $errors[0]->getMessage(),  'unexpected error message');
    }

}