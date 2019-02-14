<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/11/18
 */


namespace Test\Functional\Billing\Data;


use PapaLocal\Billing\Data\BillingProfileHydrator;
use PapaLocal\Entity\Billing\BillingProfile;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class BillingProfileHydratorTest.
 *
 * @package Test\Functional\Billing\Data
 */
class BillingProfileHydratorTest extends WebDatabaseTestCase
{
    /**
     * @var BillingProfileHydrator
     */
    private $hydrator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {

	    $this->configureDataSet([
	    	'Person',
		    'User',
		    'Address',
		    'BillingProfile',
		    'JournalSuccess',
		    'CreditCard',
		    'L_CreditCardType'
	    ]);

	    parent::setUp();

	    $this->hydrator = $this->diContainer->get('PapaLocal\Billing\Data\BillingProfileHydrator');

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Cannot hydrate instance of)/
     */
    public function testSetEntityThrowsExceptionWhenUserIdNotPresent()
    {
        // set up fixtures
        $billingProfile = new BillingProfile();

        // exercise SUT
        $this->hydrator->setEntity($billingProfile);
    }

    public function testHydrateReturnsExpectedResultOnSuccess()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT * FROM BillingProfile bp WHERE customerId IN (SELECT customerId FROM CreditCard) LIMIT 1')
            ->getRow(0);

        $creditCardArr = $this->getConnection()
            ->createQueryTable('cc', 'SELECT * FROM v_user_payment_profile WHERE userId = ' . $billingProfile['userId'])
            ->getRow(0);

        $billingProfile = (new BillingProfile())
            ->setUserId($billingProfile['userId']);

        // exercise SUT
        $this->hydrator->setEntity($billingProfile);
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertInstanceOf(BillingProfile::class, $result, 'unexpected type');
        $this->assertObjectHasAttribute('paymentProfile', $result, 'missing payment profile');
        $this->assertInstanceOf(CreditCard::class, $result->getPaymentProfile()->get(0),
            'no profiles present');
        $this->assertSame($creditCardArr['accountNumber'], $result->getPaymentProfile()->get(0)->getCardNumber(),
            'profile mismatch');
        $this->assertGreaterThan(0, $result->getPaymentProfile()->all());
    }

    public function testHydrateBillingProfileReturnsZeroBalanceWhenUserHasNone()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('billing_profile', 'SELECT * FROM BillingProfile bp WHERE customerId IN (SELECT customerId FROM CreditCard) LIMIT 1')
            ->getRow(0);

        $billingProfile = (new BillingProfile())
            ->setUserId($billingProfile['userId']);

        // exercise SUT
        $this->hydrator->setEntity($billingProfile);
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertEquals(0.00, $result->getBalance(), 'unexpected balance');
    }

    public function testHydrateBillingProfileReturnsCorrectUserBalance()
    {
        // set up fixtures
        $billingProfile = $this->getConnection()
            ->createQueryTable('billing_profile',
                'SELECT * FROM BillingProfile bp WHERE userId IN (SELECT userId FROM JournalSuccess) LIMIT 1')
            ->getRow(0);

        $billingProfile = (new BillingProfile())
            ->setUserId($billingProfile['userId']);

        // exercise SUT
        $this->hydrator->setEntity($billingProfile);
        $result = $this->hydrator->hydrate();

        // make assertions
        $this->assertNotEquals(0.00, $result->getBalance(), 'unexpected balance');
    }
}