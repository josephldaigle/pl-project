<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/15/18
 * Time: 12:28 PM
 */

namespace Test\Functional\Billing\Service;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\AddressInterface;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Entity\User;
use PapaLocal\Billing\Service\BillingProfileManager;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * BillingProfileManagerTest.
 *
 * @package Test\Functional\Billing\Service
 */
class BillingProfileManagerTest extends WebDatabaseTestCase
{
    /**
     * @var BillingProfileManager
     */
    private $billingProfileManager;

    /**
     * @var AuthorizeDotNet
     */
    private $authNet;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
    	$this->configureDataSet([
    		'Person',
		    'User',
		    'EmailAddress',
		    'L_EmailAddressType',
		    'R_PersonEmailAddress',
		    'L_CreditCardType',
		    'L_AddressType',
		    'Address',
		    'R_PersonAddress',
		    'BillingProfile',
		    'CreditCard'
	    ]);

        parent::setUp();

        $this->billingProfileManager = $this->diContainer->get('PapaLocal\Billing\Service\BillingProfileManager');
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
     * @expectedException PapaLocal\Entity\Exception\AuthorizeDotNetOperationException
     * @expectedExceptionMessageRegExp /(The credit card number is invalid)/
     */
    public function testSaveCreditCardForUserThrowsExceptionWhenAuthorizeNetCannotCreatePaymentProfile()
    {

        // set up fixtures
        $userRow = $this->getConnection()
            ->createQueryTable('user_row',
                'SELECT * FROM v_user WHERE userId NOT IN (SELECT userId FROM BillingProfile)')
            ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

        // create AuthNet cust profile in order to trigger error
        $custProfResponse = $this->authNet->createCustomerProfile($user->getFirstName(), $user->getLastName(),
            $user->getUsername());
        $this->assertTrue(is_numeric($custProfResponse));

        $address = $this->createMock(AddressInterface::class);

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(4004160088005555)
            ->setCardType(CreditCard::CARD_TYPE_VISA)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address);

        // exercise SUT
        $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);
    }

    public function testCanSaveCreditCardForUserWithoutBillingProfile()
    {
        // set up fixtures
        $begCreditCardRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');
        $begBillingProfileRowCount = $this->getConnection()->getRowCount('BillingProfile');

        $userRow = $this->getConnection()
            ->createQueryTable('user_row',
                'SELECT * FROM v_user WHERE userId NOT IN (SELECT userId FROM BillingProfile) AND isActive=1')
            ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

        $addressRow = $this->getConnection()
            ->createQueryTable('address', 'SELECT * FROM v_user_address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setId($addressRow['id'])
            ->setStreetAddress($addressRow['streetAddress'])
            ->setCity($addressRow['city'])
            ->setState($addressRow['state'])
            ->setPostalCode($addressRow['postalCode'])
            ->setType($addressRow['type'])
            ->setCountry($addressRow['country']);

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(370000000000002)
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address);

        // assert first account created correctly
        $result = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);

        $this->assertTrue(is_numeric($result), 'unexpected type');
        $this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount + 1,
            'BillingProfile table not incremented.');
        $this->assertTableRowCount('Address', $begAddressRowCount,
            'unexpected Address table row count');
        $this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 1,
            'CreditCard table not incremented.');

        // set up second card
        $addressRow2 = $this->getConnection()
            ->createQueryTable('address', 'SELECT * FROM v_user_address LIMIT 1')
            ->getRow(0);

        $address2 = (new Address())
            ->setId($addressRow2['id'])
            ->setStreetAddress($addressRow2['streetAddress'])
            ->setCity($addressRow2['city'])
            ->setState($addressRow2['state'])
            ->setPostalCode($addressRow2['postalCode'])
            ->setType($addressRow2['type'])
            ->setCountry($addressRow2['country']);

        $creditCard2 = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(6011000000000012)
            ->setCardType(CreditCard::CARD_TYPE_DISCOVER)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address2);

        // exercise SUT
        $result2 = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard2);

        // make assertions
        $this->assertTrue(is_numeric($result2), 'unexpected type');
        $this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount + 1,
            'BillingProfile table not incremented.');
        $this->assertTableRowCount('Address', $begAddressRowCount,
            'unexpected Address table row count');
        $this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 2,
            'CreditCard table not incremented.');
    }

    public function testCanSaveCreditCardForUserWithBillingProfile()
    {
        $this->markTestIncomplete('Not easy to test because we have to mock authnet, and this is testing the whole process');

        // set up fixtures
        $begCreditCardRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');
        $begBillingProfileRowCount = $this->getConnection()->getRowCount('BillingProfile');

        // fetch user that has billing profile
        $userRow = $this->getConnection()
            ->createQueryTable('user_row',
                'SELECT * FROM v_user WHERE userId IN (SELECT userId FROM BillingProfile)')
            ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

        // fetch existing address
        $addressRow = $this->getConnection()
            ->createQueryTable('address', 'SELECT * FROM Address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setId($addressRow['id'])
            ->setStreetAddress($addressRow['streetAddress'])
            ->setCity($addressRow['city'])
            ->setState($addressRow['state'])
            ->setPostalCode($addressRow['postalCode'])
            ->setCountry($addressRow['country']);

        // create a new credit card obj
        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(370000000000002)
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address);


        // add billing profile to database and authorize.net
        $custProfResponse = $this->authNet->createCustomerProfile($user->getFirstName(), $user->getLastName(),
            $user->getUsername());
        $this->assertTrue(is_numeric($custProfResponse));

        // exercise SUT
        $creditCard->setCustomerId($custProfResponse);
        $result = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);

        // make assertions
        $this->assertTrue(is_numeric($result), 'unexpected type');
        $this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount,
            'unexpected BillingProfile table row count');
        $this->assertTableRowCount('Address', $begAddressRowCount,
            'unexpected Address table row count');
        $this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 1,
            'CreditCard table not incremented.');
    }

    public function testCanSaveCreditCardForUserWithExistingAddress()
    {
        // set up fixtures
        $begCreditCardRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');
        $begBillingProfileRowCount = $this->getConnection()->getRowCount('BillingProfile');

        $userRow = $this->getConnection()
            ->createQueryTable('user_row',
                'SELECT * FROM v_user WHERE userId NOT IN (SELECT userId FROM BillingProfile)')
            ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

        $addressRow = $this->getConnection()
            ->createQueryTable('address', 'SELECT * FROM v_user_address LIMIT 1')
            ->getRow(0);

        $address = (new Address())
            ->setId($addressRow['id'])
            ->setStreetAddress($addressRow['streetAddress'])
            ->setCity($addressRow['city'])
            ->setState($addressRow['state'])
            ->setPostalCode($addressRow['postalCode'])
            ->setCountry($addressRow['country']);

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(370000000000002)
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address);

        // exercise SUT
        $result = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);

        // make assertions
        $this->assertTrue(is_numeric($result), 'unexpected type');
        $this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount + 1,
            'BillingProfile table not incremented.');
        $this->assertTableRowCount('Address', $begAddressRowCount,
            'unexpected Address table row count');
        $this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 1,
            'CreditCard table not incremented.');
    }

    public function testCanSaveCreditCardForUserWithNewAddress()
    {
        // set up fixtures
        $begCreditCardRowCount = $this->getConnection()->getRowCount('CreditCard');
        $begAddressRowCount = $this->getConnection()->getRowCount('Address');
        $begBillingProfileRowCount = $this->getConnection()->getRowCount('BillingProfile');

        $userRow = $this->getConnection()
            ->createQueryTable('user_row',
                'SELECT * FROM v_user WHERE userId NOT IN (SELECT userId FROM BillingProfile)')
            ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

        $address = (new Address())
            ->setStreetAddress('444 Topfloor Dr')
            ->setCity('Sometownin')
            ->setState('Alabama')
            ->setPostalCode('22222')
            ->setCountry('United States');

        $creditCard = (new CreditCard())
            ->setFirstName('Guy')
            ->setLastName('Tester')
            ->setCardNumber(370000000000002)
            ->setCardType(CreditCard::CARD_TYPE_AMEX)
            ->setExpirationMonth(12)
            ->setExpirationYear(2024)
            ->setIsDefaultPayMethod(true)
            ->setAddress($address);

        // exercise SUT
        $result = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);

        // make assertions
        $this->assertTrue(is_numeric($result), 'unexpected type');
        $this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount + 1,
            'BillingProfile table not incremented.');
        $this->assertTableRowCount('Address', $begAddressRowCount + 1,
            'Address table not incremented.');
        $this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 1,
            'CreditCard table not incremented.');
    }

	public function testSaveCreditCardForUserOnlySavesLastFourDigitsOfCardNumber()
	{
		// set up fixtures
		$begCreditCardRowCount = $this->getConnection()->getRowCount('CreditCard');
		$begAddressRowCount = $this->getConnection()->getRowCount('Address');
		$begBillingProfileRowCount = $this->getConnection()->getRowCount('BillingProfile');

		$userRow = $this->getConnection()
		                ->createQueryTable('user_row',
			                'SELECT * FROM v_user WHERE userId NOT IN (SELECT userId FROM BillingProfile)')
		                ->getRow(0);

        $person = new Person(new Guid($userRow['personGuid']), $userRow['firstName'], $userRow['lastName']);

        $user = (new User())
            ->setId($userRow['userId'])
            ->setUsername($userRow['username'])
            ->setPerson($person);

		$addressRow = $this->getConnection()
		                   ->createQueryTable('address', 'SELECT * FROM v_user_address LIMIT 1')
		                   ->getRow(0);

		$address = (new Address())
			->setId($addressRow['id'])
			->setStreetAddress($addressRow['streetAddress'])
			->setCity($addressRow['city'])
			->setState($addressRow['state'])
			->setPostalCode($addressRow['postalCode'])
			->setCountry($addressRow['country']);

		$creditCard = (new CreditCard())
			->setFirstName('Guy')
			->setLastName('Tester')
			->setCardNumber(370000000000002)
			->setCardType(CreditCard::CARD_TYPE_AMEX)
			->setExpirationMonth(12)
			->setExpirationYear(2024)
			->setIsDefaultPayMethod(true)
			->setAddress($address);

		// exercise SUT
		$result = $this->billingProfileManager->saveCreditCardForUser($user, $creditCard);

		// make assertions
		$this->assertTrue(is_numeric($result), 'unexpected type');
		$this->assertTableRowCount('BillingProfile', $begBillingProfileRowCount + 1,
			'BillingProfile table not incremented.');
		$this->assertTableRowCount('Address', $begAddressRowCount,
			'unexpected Address table row count');
		$this->assertTableRowCount('CreditCard', $begCreditCardRowCount + 1,
			'CreditCard table not incremented.');

		$savedCard = $this->getConnection()
			->createQueryTable('saved_card', 'SELECT * FROM CreditCard WHERE id =' . $result)
			->getRow(0);

		$this->assertEquals('0002', $savedCard['accountNumber'], 'unexpected account number');
	}
}