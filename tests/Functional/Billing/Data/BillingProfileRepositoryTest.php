<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 3/29/18
 */

namespace Test\Functional\Billing\Data;


use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Test\WebDatabaseTestCase;
use PapaLocal\Billing\RechargeSetting;


/**
 * Class BillingProfileRepositoryTest.
 *
 * @package Test\Functional\Data\Command\Service
 */
class BillingProfileRepositoryTest extends WebDatabaseTestCase
{
    /**
     * @var BillingProfileRepository
     */
    private $billingProfileRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
	    $this->configureDataSet();

	    parent::setUp();

        $this->billingProfileRepository = $this->diContainer->get('PapaLocal\Billing\Data\BillingProfileRepository');
    }

    public function testHasProfileReturnsTrueWhenProfileExists()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,'SELECT * FROM v_user_billing_profile LIMIT 1')
            ->getRow(0)['userId'];

        // exercise SUT
        $result = $this->billingProfileRepository->hasBillingProfile($userId);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(true, $result, 'unexpected value');
    }

    public function testHasProfileReturnsFalseWhenProfileNotExists()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,
                'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM v_user_billing_profile) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $result = $this->billingProfileRepository->hasBillingProfile($userId);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(false, $result, 'unexpected value');

    }

    public function testHasActiveBillingProfileReturnsTrueOnSuccess()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,'SELECT * FROM v_user_billing_profile WHERE isActive = 1 LIMIT 1')
            ->getRow(0)['userId'];

        // exercise SUT
        $result = $this->billingProfileRepository->hasActiveBillingProfile($userId);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(true, $result, 'unexpected value');

    }

    public function testHasActiveBillingProfileReturnsFalseWhenProfileInactive()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,'SELECT * FROM BillingProfile WHERE isActive = 0 LIMIT 1')
            ->getRow(0)['userId'];

        // exercise SUT
        $result = $this->billingProfileRepository->hasActiveBillingProfile($userId);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(false, $result, 'unexpected value');
    }

    public function testHasActiveBillingProfileReturnsFalseWhenProfileNotFound()
    {
        // set up fixtures

        // fetch a user without a billing profile
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,
                'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM v_user_billing_profile) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $result = $this->billingProfileRepository->hasActiveBillingProfile($userId);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(false, $result, 'unexpected value');

    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(User)(.)+(already has a billing profile)/
     */
    public function testCreateBillingProfileThrowsExceptionWhenProfileExists()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,'SELECT userId FROM v_user_billing_profile LIMIT 1')
            ->getRow(0)['userId'];

        // exercise SUT
        $this->billingProfileRepository->createBillingProfile($userId, 99999999999999);
    }

    public function testCreateBillingProfileCreatesProfileOnSuccess()
    {
        // set up fixtures
        $begBillProCount = $this->getConnection()->getRowCount('BillingProfile');

        // fetch a user without a billing profile
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM BillingProfile) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $result = $this->billingProfileRepository->createBillingProfile($userId, 99999999999999);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertGreaterThan(1, $result, 'unexpected value');
        $this->assertTableRowCount('BillingProfile', $begBillProCount + 1,
            'BillingProfile table not incremented');
    }

    public function testLoadUserBillingProfileReturnsEmptyProfileNoProfileExists()
    {
        // set up fixtures
        $userId = $this->getConnection()
            ->createQueryTable('user_id',
                'SELECT id FROM User WHERE id NOT IN (SELECT userId from v_user_billing_profile) LIMIT 1')
            ->getRow(0)['id'];


        // exercise SUT
        $result = $this->billingProfileRepository->loadBillingProfile($userId);

        $this->assertInstanceOf(BillingProfile::class, $result, 'unexpected type');
        $this->assertNull($result->getId(), 'billing profile not empty');
    }

    public function testLoadUserBillingProfileReturnsBillingProfileOnSuccess()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT * FROM v_user_billing_profile LIMIT 1')
            ->getRow(0);

        // exercise SUT
        $result = $this->billingProfileRepository->loadBillingProfile($billingProfile['userId']);

        // make assertions
        $this->assertInstanceOf(BillingProfile::class, $result, 'unexpected type');
        $this->assertSame($billingProfile['customerId'], $result->getCustomerId(), 'unexpected value');
        $this->assertNull($result->getPaymentProfile(), 'payment profile should not be present');
    }

    public function testLoadUserBillingProfileAddsPaymentProfileWhenRequested()
    {
        // set up fixtures
        // fetch billing profile that is associated to payment profile
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id',
                'SELECT * FROM BillingProfile WHERE customerId IN (SELECT customerId FROM CreditCard) LIMIT 1')
            ->getRow(0);
        $creditCardArr = $this->getConnection()
            ->createQueryTable('credit_card',
                'SELECT * FROM CreditCard WHERE customerId =' . $billingProfile['customerId'])
            ->getRow(0);

        // exercise SUT
        $result = $this->billingProfileRepository->loadBillingProfile($billingProfile['userId'], true);

        // make assertions
        $this->assertInstanceOf(BillingProfile::class, $result, 'unexpected type');
        $this->assertSame($billingProfile['customerId'], $result->getCustomerId(), 'unexpected value');
        $this->assertNotNull($result->getPaymentProfile(), 'payment profile is not present');

        // assert pay profile
        $paymentProfile = $result->getPaymentProfile();
        $this->assertInstanceOf(Collection::class, $paymentProfile, 'unexpected type');
        $this->assertGreaterThan(0, $paymentProfile->count(), 'no profiles present');
        $this->assertInstanceOf(CreditCard::class, $paymentProfile->get(0), 'unexpected type');
        $this->assertSame($creditCardArr['accountNumber'], $paymentProfile->get(0)->getCardNumber(), 'unexpected value');
    }

	public function testHasDefaultPayMethodReturnsTrueWhenFound()
	{
		// set up fixtures
		$paymentProfile = $this->getConnection()
			->createQueryTable('payment_profile', 'SELECT * FROM v_user_payment_profile WHERE defaultPayMethod = 1 LIMIT 1')
			->getRow(0);

		// exercise SUT
		$result = $this->billingProfileRepository->hasDefaultPayMethod($paymentProfile['userId']);

		// make assertions
		$this->assertTrue(is_bool($result), 'unexpected type');
		$this->assertTrue($result, 'unexpected value');
    }

    public function testHasDefaultPayMethodReturnsFalseWhenNotFound()
    {
	    // set up fixtures
	    $userId = intval($this->getConnection()
	                          ->createQueryTable('user', 'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM v_user_payment_profile)')
	                          ->getRow(0)['id']);

	    // exercise SUT
	    $result = $this->billingProfileRepository->hasDefaultPayMethod($userId);

	    // make assertions
	    $this->assertTrue(is_bool($result), 'unexpected type');
	    $this->assertFalse($result, 'unexpected value');
    }

    public function testLoadUserBillingProfileReturnsEmptyPaymentProfileWhenNoneFound()
    {
        // set up fixtures
        // fetch billing profile that is associated to payment profile
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id',
                'SELECT * FROM BillingProfile WHERE customerId NOT IN (SELECT customerId FROM CreditCard) LIMIT 1')
            ->getRow(0);

        // exercise SUT
        $result = $this->billingProfileRepository->loadBillingProfile($billingProfile['userId'], true);

        // make assertions
        $this->assertInstanceOf(BillingProfile::class, $result, 'unexpected type');
        $this->assertSame($billingProfile['customerId'], $result->getCustomerId(), 'unexpected value');
        $this->assertEquals(0, $result->getPaymentProfile()->count(), 'payment profile list not empty');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(The user)(.)+(does not have a billing profile)/
     */
    public function testDeactivateBillingProfileThrowsExceptionWhenNoProfileFound()
    {
        // set up fixtures
        // fetch a user without a billing profile
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,
                'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM BillingProfile) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $this->billingProfileRepository->deactivateBillingProfile($userId);
    }

    public function testDeactivateBillingProfileDeactivatesProfileOnSuccess()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT * FROM BillingProfile WHERE isActive = 1 LIMIT 1')
            ->getRow(0);

        // exercise SUT
        $result = $this->billingProfileRepository->deactivateBillingProfile($billingProfile['userId']);

        $newActiveFlag = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT isActive FROM BillingProfile WHERE userId = ' . $billingProfile['userId'])
            ->getRow(0)['isActive'];

        // make assertions
        // assert result value
        $this->assertEquals(1, $result, 'unexpected num rows affected');
        // assert database value
        $this->assertEquals(0, $newActiveFlag, 'unexpected value');
    }

    public function testActivateBillingProfileIsSuccess()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT * FROM BillingProfile WHERE isActive = 0 LIMIT 1')
            ->getRow(0);

        // exercise SUT
        $result = $this->billingProfileRepository->activateBillingProfile($billingProfile['userId']);

        $newActiveFlag = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT isActive FROM BillingProfile WHERE userId = ' . $billingProfile['userId'])
            ->getRow(0)['isActive'];

        // make assertions
        $this->assertEquals(1, $result, 'unexpected num rows affected');
        $this->assertEquals(1, $newActiveFlag, 'unexpected value');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(The user)(.)+(does not have a billing profile)/
     */
    public function testActivateBillingProfileThrowsExceptionWhenBillingProfileDoesNotExist()
    {
        // set up fixtures
        // fetch a user without a billing profile
        $userId = $this->getConnection()
            ->createQueryTable('user_id' ,
                'SELECT id FROM User WHERE id NOT IN (SELECT userId FROM BillingProfile) LIMIT 1')
            ->getRow(0)['id'];

        // exercise SUT
        $this->billingProfileRepository->activateBillingProfile($userId);
    }

    public function testCreditCardExistsReturnsFalseWhenCardNotFound()
    {
        // exercise SUT
        $result = $this->billingProfileRepository->creditCardExists(9999999);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(false, $result, 'unexpected value');
    }

    public function testCreditCardExistsReturnsTrueWhenCardFound()
    {
        // set up fixtures
        // fetch a user without a billing profile
        $ccNumber = $this->getConnection()
            ->createQueryTable('credit_card' ,'SELECT accountNumber FROM CreditCard LIMIT 1')
            ->getRow(0)['accountNumber'];

        // exercise SUT
        $result = $this->billingProfileRepository->creditCardExists($ccNumber);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(true, $result, 'unexpected value');
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @expectedExceptionMessageRegExp /^(Unable to delete credit card)/
     */
    public function testDeleteCreditCardThrowsExceptionWhenCardNotFound()
    {
        // set up fixtures
        // fetch a user without a billing profile
        $creditCard = (new CreditCard())
            ->setCustomerId(123451234512345);

        // exercise SUT
        $this->billingProfileRepository->deleteCreditCard($creditCard);
    }

    public function testDeleteCreditCardReturnsCorrectNumRowsOnSuccess()
    {
        // set up fixtures
        $begRowCount = $this->getConnection()->getRowCount('CreditCard');

        // fetch a user without a billing profile
        $ccRow = $this->getConnection()
            ->createQueryTable('credit_card' ,'SELECT * FROM CreditCard LIMIT 1')
            ->getRow(0);

        $creditCard = (new CreditCard())
            ->setId($ccRow['id'])
            ->setCustomerId($ccRow['customerId']);

        // exercise SUT
        $result = $this->billingProfileRepository->deleteCreditCard($creditCard);

        // make assertions
        $this->assertTrue(is_int($result), 'unexpected type');
        $this->assertEquals(1, $result, 'unexpected value');
        $this->assertTableRowCount('CreditCard', $begRowCount -1, 'unexpected row count');

    }

    /**
     * @expectedException PapaLocal\Entity\Exception\ServiceOperationFailedException
     * @expectedExceptionMessageRegExp /^(Unable to set default pay method for customer)(.)+(no cards found)/
     */
    public function testSetAsDefaultPaymentMethodThrowsExceptionWhenCustomerHasNoCardsSaved()
    {
        // set up fixtures
        $customerId = $this->getConnection()
            ->createQueryTable('cust_id',
                'SELECT customerId FROM BillingProfile WHERE customerId NOT IN (SELECT customerId FROM CreditCard)')
            ->getRow(0)['customerId'];

        $creditCard = (new CreditCard())
            ->setCustomerId($customerId);

        // exercise SUT
        $this->billingProfileRepository->setAsDefaultPaymentMethod($creditCard);
    }

    public function testSetAsDefaultPaymentMethodReplacesExistingDefaultPayMethodOnSuccess()
    {
        // set up fixtures
        // fetch credit card from database
        $ccRow = $this->getConnection()
            ->createQueryTable('credit_card' ,'SELECT * FROM CreditCard WHERE defaultPayMethod = 0 LIMIT 1')
            ->getRow(0);

        $creditCard = (new CreditCard())
            ->setId($ccRow['id'])
            ->setCustomerId($ccRow['customerId']);

        // exercise SUT
        $result = $this->billingProfileRepository->setAsDefaultPaymentMethod($creditCard);

        // make assertions
        $this->assertTrue(is_bool($result), 'unexpected type');
        $this->assertEquals(true, $result, 'unexpected value');

        $postUpdateCard = $this->getConnection()
            ->createQueryTable('credit_card', 'SELECT * FROM CreditCard WHERE id = ' . $ccRow['id']);

        $this->assertEquals(1, $postUpdateCard->getRowCount(), 'unexpected row count');
        $this->assertEquals(1, $postUpdateCard->getRow(0)['defaultPayMethod'], 'unexpected default pay method');
    }


}