<?php
/**
 * Created by eWebify, LLC.
 * Creator: Joe Daigle
 * Date: 12/13/17
 */


namespace Test\Functional\AuthorizeDotNet;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Billing\Entity\BankAccount;
use PapaLocal\Billing\Entity\BankAccountInterface;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Billing\CreditCardInterface;
use PapaLocal\Entity\Exception\AuthorizeDotNetOperationException;
use net\authorize\api\contract\v1\CreateTransactionResponse;
use net\authorize\api\contract\v1\CustomerPaymentProfileMaskedType;
use net\authorize\api\contract\v1\GetCustomerProfileResponse;
use PapaLocal\Test\WebTestCase;


/**
 * Class AuthorizeNetTest.
 *
 * Tests interactions with the Authorize.Net API.
 *
 */
class AuthorizeDotNetTest extends WebTestCase
{

    /**
     * @var AuthorizeDotNet
     */
    private $authNet;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->authNet = $this->diContainer->get('PapaLocal\AuthorizeDotNet\AuthorizeDotNet');
    }

    /**
     * Removes customer profiles created in AuthNet when testing.
     *
     * @inheritdoc
     */
    protected function tearDown()
    {
        /**
         * Remove all remote customer profiles from Authorize.net.
         */
        // delete profiles created during testing
        $ids = $this->authNet->fetchCustomerProfileIds();
        if ($ids) {
            foreach ($ids as $id) {
                $this->authNet->deleteCustomerProfile($id);
            }
        }

        //call parent method
        parent::tearDown();
    }

    /**
     * test createCustomerProfile
     */

    public function testCreateCustomerProfileReturnsProfileIdOnSuccess()
    {
        // exercise SUT
        $response = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');

        //make assertions
        $this->assertTrue(is_numeric($response));
    }

    public function testCreateCustomerProfileReturnsFalseWhenProfileExistsForUsername()
    {
        //exercise SUT
        $custProfResponse1 = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'anotherOne@email.com');
        $this->assertTrue(is_numeric($custProfResponse1));

        $response = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'anotherOne@email.com');

        //make assertions
        $this->assertEquals(false, $response);
    }


    public function testDeleteCustomerProfileReturnsSuccessResponse()
    {
        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'guyt@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        //exercise SUT
        $deleteResponse = $this->authNet->deleteCustomerProfile(intval($custProfResponse));

        //make assertions
        $this->assertEquals(true, $deleteResponse);
    }


    public function testDeleteCustomerProfileReturnsErrorResponseWhenProfileDoesNotExist()
    {
        //exercise SUT
        $deleteResponse = $this->authNet->deleteCustomerProfile(0000);


        //make assertions
        $this->assertEquals(false, $deleteResponse);
    }

    public function testFetchCustomerProfileReturnsFalseWhenEmptyUsernameSupplied()
    {
        //exercise SUT
        $response = $this->authNet->fetchCustomerProfile('');

        //make assertions
        $this->assertEquals(false, $response);
    }

    public function testFetchCustomerProfileReturnsFalseWhenProfileNotFound()
    {
        //exercise SUT
        $response = $this->authNet->fetchCustomerProfile('notReal@email.com');

        //make assertions
        $this->assertEquals(false, $response);
    }

    public function testFetchCustomerProfileReturnsProfileOnSuccess()
    {
        //set up fixtures
        $firstName = 'Guy';
        $lastName = 'Tester';
        $email = 'gtester@email.com';

        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile($firstName, $lastName, $email);
        $this->assertTrue(is_numeric($custProfResponse));

        //exercise SUT
        $response = $this->authNet->fetchCustomerProfile('gtester@email.com');

        //make assertions
        $this->assertInstanceOf(GetCustomerProfileResponse::class, $response);
        $this->assertSame($custProfResponse, $response->getProfile()->getCustomerProfileId());
        $this->assertSame($lastName . ', ' . $firstName, $response->getProfile()->getDescription());
        $this->assertSame($email, $response->getProfile()->getEmail());
    }

    public function testFetchCustomerProfileIdsReturnsEmptyArrayWhenNoneExist()
    {
        //exercise SUT
        $response = $this->authNet->fetchCustomerProfileIds();

        //make assertions
        $this->assertTrue(is_array($response));
        $this->assertEquals(count($response), 0);
    }

    public function testFetchCustomerProfileIdsReturnsArrayOfIdsOnSuccess()
    {
        // set up fixtures
        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile('Freddy', 'Oarnaught',
            'foarnaught@email.com');
        $this->assertTrue(is_numeric($custProfResponse));
        $custProfResponse2 = $this->authNet->createCustomerProfile('Fanny', 'Mae',
            'fmae@email.com');
        $this->assertTrue(is_numeric($custProfResponse2));

        //exercise SUT
        $response = $this->authNet->fetchCustomerProfileIds();

        //make assertions
        $this->assertTrue(is_array($response));
        $this->assertEquals(count($response), 2);
        $this->assertSame($custProfResponse, $response[0]);
        $this->assertSame($custProfResponse2, $response[1]);
    }

    public function testFetchCustomerProfileResultContainsPaymentProfiles()
    {
        //set up fixtures
        $firstName = 'Guy';
        $lastName = 'Tester';
        $email = 'gtester@email.com';

        //create a customer profile
        $createCustProfResp = $this->authNet->createCustomerProfile($firstName, $lastName, $email);
        $this->assertTrue(is_numeric($createCustProfResp));

        // create a payment profile
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // create the first profile
        $createPayProResp = $this->authNet->createCreditCardProfile($createCustProfResp, $creditCard);

        // make assertions
        $this->assertTrue(is_bool($createPayProResp), 'unexpected type');
        $this->assertSame(true, $createPayProResp, 'unexpected value');

        // create a payment profile
        $creditCard2 = (new CreditCard())
            ->setCardNumber('6011000000000012')
            ->setCardType(CreditCard::CARD_TYPE_DISCOVER)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(212)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(false);

        // create the first profile
        $createPayProResp2 = $this->authNet->createCreditCardProfile($createCustProfResp, $creditCard2);

        // make assertions
        $this->assertTrue(is_bool($createPayProResp2), 'unexpected type');
        $this->assertSame(true, $createPayProResp2, 'unexpected value');

        //exercise SUT
        $response = $this->authNet->fetchCustomerProfile('gtester@email.com');

        //make assertions
        $this->assertInstanceOf(GetCustomerProfileResponse::class, $response);
        $this->assertSame($createCustProfResp, $response->getProfile()->getCustomerProfileId());
        $this->assertSame($lastName . ', ' . $firstName, $response->getProfile()->getDescription());
        $this->assertSame($email, $response->getProfile()->getEmail());

        $this->assertNotNull($response->getProfile()->getPaymentProfiles(), 'paymentProfiles not set');
        $this->assertNotEmpty($response->getProfile()->getPaymentProfiles(), 'paymentProfiles not set');
        $this->assertCount(2, $response->getProfile()->getPaymentProfiles(),
            'unexpected payment profile count');
    }

    /**
     * updateCustomerProfile
     */

    public function testUpdateCustomerProfileReturnsFalseWhenUsernameNotSupplied()
    {
        // exercise SUT
        $response = $this->authNet->updateCustomerProfile(12345678, '');

        // make assertions
        $this->assertEquals(false, $response);
    }

    public function testUpdateCustomerProfilePerformsUpdateOnSuccess()
    {
        // set up fixtures
        $newEmailAddress = 'someNewEmail@ewebify.com';

        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // exercise SUT
        $updateResponse = $this->authNet->updateCustomerProfile($custProfResponse, $newEmailAddress);

        // assert update response result
        $this->assertSame('Ok', $updateResponse->getMessages()->getResultCode(),
            'unable to update customer profile');
        $this->assertContains('Successful',
            $updateResponse->getMessages()->getMessage()[0]->getText(),
            'unexpected response message');

        // execute fetch response to validate profile change
        $fetchResponse = $this->authNet->fetchCustomerProfile($newEmailAddress);

        // assert fetch response reflects change in email address
        $this->assertSame('Ok', $fetchResponse->getMessages()->getResultCode());
        $this->assertContains('Successful',
            $fetchResponse->getMessages()->getMessage()[0]->getText(),
            'unexpected response message');
        $this->assertSame($newEmailAddress, $fetchResponse->getProfile()->getEmail(),
            'email address mismatch - profile not updated');
    }

    public function testCreatePaymentProfileWithCreditCardSavesProfileOnSuccess()
    {
        // set up fixtures
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // exercise SUT
        $result = $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');
    }

	/**
	 * @expectedException PapaLocal\Entity\Exception\AuthorizeDotNetOperationException
	 * @expectedExceptionMessageRegExp /(A duplicate customer payment profile already exists)/
	 */
    public function testCreatePaymentProfileWithCreditCardThrowsExceptionWhenProfileExists()
    {
        // set up fixtures
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // create the first profile
        $result = $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');


        // create the first profile
        $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);
    }

    public function testDeleteCreditCardProfileReturnsTrueWhenCustomerProfileNotFound()
    {
        // set up fixtures

        // create a credit card
        $creditCardMock = $this->createMock(CreditCardInterface::class);

        // exercise SUT
        $result = $this->authNet->deleteCreditCardProfile('badEmail@email.com', $creditCardMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');
    }

    public function testFetchCreditCardProfileReturnsFalseWhenCustomerProfileDoesNotExistForUser()
    {
        // set up fixtures
        $ccMock = $this->createMock(CreditCardInterface::class);

        // exercise SUT
        $result = $this->authNet->fetchCreditCardProfile('badEmail@email.com', $ccMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');
    }

    public function testFetchCreditCardProfileReturnsFalseWhenUserHasZeroPaymentProfiles()
    {
        // set up fixtures
        $ccMock = $this->createMock(CreditCardInterface::class);

        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // exercise SUT
        $result = $this->authNet->fetchCreditCardProfile('testemail@email.com', $ccMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');

    }

    public function testFetchCreditCardProfileReturnsFalseWhenNotFound()
    {
        // set up fixtures

        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);

        // make assertions
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        //exercise SUT
        $creditCard->setCardNumber('370000000000000');
        $result = $this->authNet->fetchCreditCardProfile('testemail@email.com', $creditCard);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');

    }

    public function testFetchCreditCardProfileReturnsProfileOnSuccess()
    {
        // set up fixtures

        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);

        // make assertions
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        //exercise SUT
        $result = $this->authNet->fetchCreditCardProfile('testemail@email.com', $creditCard->setCardNumber('0002'));

        // make assertions
        $this->assertInstanceOf(CustomerPaymentProfileMaskedType::class, $result, 'unexpected type');

    }

    /**
     * @expectedException PapaLocal\Entity\Exception\AuthorizeDotNetOperationException
     * @expectedExceptionMessageRegExp /(Unable to charge card)(.)+(The transaction amount submitted was greater than the maximum amount allowed)/
     */
    public function testChargeCreditCardThrowsExceptionWhenAmountTooHigh()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(4444)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = number_format( (float) time(), 2, '.', '');

        // exercise SUT
        $this->authNet->chargeCreditCard('testemail@email.com', $creditCard->setCardNumber('0002'), $amount);
    }

	/**
	 * @expectedException PapaLocal\Billing\Exception\DuplicateTransactionException
	 * @expectedExceptionMessageRegExp /^(Unable to process duplicate transaction for)/
	 */
	public function testChargeCreditCardThrowsExceptionWhenDuplicateTransaction()
	{
		// create a cust profile
		$custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
			'testemail@email.com');
		$this->assertTrue(is_numeric($custProfResponse));

		// create a credit card
		$creditCard = (new CreditCard())
			->setCardNumber('370000000000002')
			->setCardType(CreditCard::CARD_TYPE_AMEX)
			->setExpirationMonth(12)
			->setExpirationYear(20)
			->setSecurityCode(4444)
			->setAddress((new Address())
				->setStreetAddress('200 Anywhere Rd')
				->setCity('Anytown')
				->setState('Alabama')
				->setCountry('United States')
				->setPostalCode(45444)
			)
			->setIsDefaultPayMethod(true);

		// save payment profile
		$savePayProResp = $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);
		$this->assertTrue(is_bool($savePayProResp), 'unable to create profile');
		$this->assertSame(true, $savePayProResp, 'unable to create profile');

		// generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
		$amount = (float) number_format(mt_rand( 4 , 9 ), 2, '.', '');

		// assert 1st charge is successful
        $chargeResp1 = $this->authNet->chargeCreditCard('testemail@email.com', $creditCard->setCardNumber('0002'), $amount);
        $this->assertInstanceOf(CreateTransactionResponse::class, $chargeResp1, 'unexpected type');
        $this->assertEquals('Successful.', $chargeResp1->getMessages()->getMessage()[0]->getText(), 'unexpected message');


        // exercise SUT
		$this->authNet->chargeCreditCard('testemail@email.com', $creditCard, $amount);
	}

    public function testChargeCreditCardReturnsExpectedResponseOnSuccess()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse), 'cannot create customer profile');

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(25)
            ->setSecurityCode(900)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = (float) number_format(mt_rand( 2 , 9 ), 2, '.', '');

        // exercise SUT
        $response = $this->authNet->chargeCreditCard('testemail@email.com', $creditCard->setCardNumber('0002'), $amount);
        $transResponse = $response->getTransactionResponse();

        // make assertions
        $this->assertInstanceOf(CreateTransactionResponse::class, $response, 'unexpected type');

        $messages = array('Your order has been received. Thank you for your business!', 'This transaction has been approved.');
        $this->assertContains($transResponse->getMessages()[0]->getDescription(), $messages, 'unexpected message');
    }

    public function testRefundCreditCardReturnsResponseOnSuccess()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'gtrefund@email.com');
        $this->assertTrue(is_numeric($custProfResponse), 'cannot create customer profile');

        // create a credit card
        $creditCard = (new CreditCard())
            ->setCardNumber('370000000000002')
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(20)
            ->setSecurityCode(900)
            ->setAddress((new Address())
                ->setStreetAddress('200 Anywhere Rd')
                ->setCity('Anytown')
                ->setState('Alabama')
                ->setCountry('United States')
                ->setPostalCode(45444)
            )
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createCreditCardProfile($custProfResponse, $creditCard);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = (float) number_format(mt_rand( 4 , 9 ), 2, '.', '');

        // exercise SUT
        $response = $this->authNet->refundCreditCard('gtrefund@email.com', $creditCard, $amount);
        $transResponse = $response->getTransactionResponse();

        // make assertions
        $this->assertInstanceOf(CreateTransactionResponse::class, $response, 'unexpected type');
        $this->assertSame('This transaction has been approved.',
            $transResponse->getMessages()[0]->getDescription(), 'unexpected value');
    }

    public function testCreatePaymentProfileWithBankAccountSavesProfileOnSuccess()
    {
        // set up fixtures
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a credit card
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // exercise SUT
        $result = $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');
    }

    public function testFetchBankAccountProfileReturnsFalseWhenCustomerProfileDoesNotExistForUser()
    {
        // set up fixtures
        $bankAccountMock = $this->createMock(BankAccountInterface::class);

        // exercise SUT
        $result = $this->authNet->fetchBankAccountProfile('badEmail@email.com', $bankAccountMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');
    }

    public function testFetchBankAccountProfileReturnsFalseWhenUserHasZeroPaymentProfiles()
    {
        // set up fixtures
        $bankAccountMock = $this->createMock(BankAccountInterface::class);

        //create a customer profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // exercise SUT
        $result = $this->authNet->fetchBankAccountProfile('testemail@email.com', $bankAccountMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');

    }

    public function testFetchBankAccountProfileReturnsFalseWhenNotFound()
    {
        // set up fixtures

        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);

        // make assertions
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        //exercise SUT
        $bankAccount->setCustomerId($custProfResponse);
        $result = $this->authNet->fetchBankAccountProfile('testemail@email.com', $bankAccount);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(false, $result, 'unexpected value');

    }

    public function testFetchBankAccountProfileReturnsProfileOnSuccess()
    {
        // set up fixtures

        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp = $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);

        // make assertions
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        //exercise SUT
        // would be fetching with the last 4 only
        $bankAccount->setAccountNumber('4634');
        $result = $this->authNet->fetchBankAccountProfile('testemail@email.com', $bankAccount);

        // make assertions
        $this->assertInstanceOf(CustomerPaymentProfileMaskedType::class, $result, 'unexpected type');

    }

    public function testDeleteBankAccountProfileReturnsTrueWhenCustomerProfileNotFound()
    {
        // set up fixtures
        // create a bank account
        $bankAccountMock = $this->createMock(BankAccountInterface::class);

        // exercise SUT
        $result = $this->authNet->deleteBankAccountProfile('badEmail@email.com', $bankAccountMock);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertSame(true, $result, 'unexpected value');
    }

    public function testChargeBankAccountReturnsExpectedResponseOnSuccess()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse), 'cannot create customer profile');

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = (float) number_format(mt_rand( 2 , 9 ), 2, '.', '');

        // exercise SUT
        // this account number would be stored in database
        $bankAccount->setAccountNumber('4634');

        $response = $this->authNet->chargeBankAccount('testemail@email.com', $bankAccount, $amount);
        $transResponse = $response->getTransactionResponse();

        // make assertions
        $this->assertInstanceOf(CreateTransactionResponse::class, $response, 'unexpected type');

        $messages = array('Your order has been received. Thank you for your business!', 'This transaction has been approved.');
        $this->assertContains($transResponse->getMessages()[0]->getDescription(), $messages, 'unexpected message');
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\AuthorizeDotNetOperationException
     * @expectedExceptionMessageRegExp /(Unable to charge account)(.)+(The transaction amount submitted was greater than the maximum amount allowed)/
     */
    public function testChargeBankAccountThrowsExceptionWhenAmountTooHigh()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = number_format( (float) time(), 2, '.', '');

        // exercise SUT
        $bankAccount->setAccountNumber('4634');
        $this->authNet->chargeBankAccount('testemail@email.com', $bankAccount, $amount);
    }

    /**
     * @expectedException PapaLocal\Billing\Exception\DuplicateTransactionException
     * @expectedExceptionMessageRegExp /^(Unable to process duplicate transaction for)/
     */
    public function testChargeBankAccountThrowsExceptionWhenDuplicateTransaction()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'testemail@email.com');
        $this->assertTrue(is_numeric($custProfResponse));

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp = $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);
        $this->assertTrue(is_bool($savePayProResp), 'unable to create profile');
        $this->assertSame(true, $savePayProResp, 'unable to create profile');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = (float) number_format(mt_rand( 4 , 9 ), 2, '.', '');

        // assert 1st charge is successful
        $bankAccount->setAccountNumber('4634');
        $chargeResp1 = $this->authNet->chargeBankAccount('testemail@email.com', $bankAccount, $amount);
        $this->assertInstanceOf(CreateTransactionResponse::class, $chargeResp1, 'unexpected type');
        $this->assertEquals('Successful.', $chargeResp1->getMessages()->getMessage()[0]->getText(), 'unexpected message');


        // exercise SUT
        $this->authNet->chargeBankAccount('testemail@email.com', $bankAccount, $amount);
    }

    public function testRefundBankAccountReturnsResponseOnSuccess()
    {
        // create a cust profile
        $custProfResponse = $this->authNet->createCustomerProfile('Guy', 'Tester',
            'gtrefund@email.com');
        $this->assertTrue(is_numeric($custProfResponse), 'cannot create customer profile');

        // create a bank account
        $bankAccount = (new BankAccount())
            ->setAccountNumber('99173827361274634')
            ->setRoutingNumber('011000206')
            ->setAccountHolder('Guy Tester')
            ->setAccountType('checking')
            ->setBankName('Bank of America')
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setAddress(new \PapaLocal\Core\ValueObject\Address('200 Anywhere Rd.', 'Anytown', 'Alabama', 'United States', 45444))
            ->setIsDefaultPayMethod(true);

        // save payment profile
        $savePayProResp= $this->authNet->createBankAccountProfile($custProfResponse, $bankAccount);
        $this->assertTrue(is_bool($savePayProResp), 'unexpected type');
        $this->assertSame(true, $savePayProResp, 'unexpected value');

        // generate the amount semi-randomly to prevent 'duplicate trans' rejection from AuthNet
        $amount = (float) number_format(mt_rand( 4 , 9 ), 2, '.', '');

        // exercise SUT
        $bankAccount->setAccountNumber('4634');
        $response = $this->authNet->refundBankAccount('gtrefund@email.com', $bankAccount, $amount);

        // make assertions
        $this->assertInstanceOf(CreateTransactionResponse::class, $response, 'unexpected type');
        $this->assertSame('Ok', $response->getMessages()->getResultCode(), 'unexpected response code');
        $this->assertSame('The transaction is currently under review.', $response->getTransactionResponse()->getMessages()[0]->getDescription(), 'unexpected transaction message');
        $this->assertEquals('Successful.', $response->getMessages()->getMessage()[0]->getText(), 'unexpected message');
    }
}