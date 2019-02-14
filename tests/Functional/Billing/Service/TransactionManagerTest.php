<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/1/18
 * Time: 1:42 PM
 */

namespace Test\Functional\Service;


use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Entity\Billing\CreditCard;
use PapaLocal\Entity\Billing\Transaction;
use PapaLocal\Entity\User;
use PapaLocal\Billing\Service\TransactionManager;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * TransactionManagerTest.
 *
 * @package Test\Functional\Service
 */
class TransactionManagerTest extends WebDatabaseTestCase
{
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;

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
			'CreditCard',
			'JournalSuccess',
			'JournalFail'
		]);

		parent::setUp();

		$this->transactionManager = $this->diContainer->get('PapaLocal\Billing\Service\TransactionManager');
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
	 * @expectedException PapaLocal\Billing\Exception\AccountNotFoundException
	 * @expectedExceptionMessageRegExp /(Credit card with id)(.)+(was not found in billing profile for customerId)/
	 */
	public function testChargeCreditCardThrowsExceptionWhenUserDoesNotOwnCard()
	{
		// set up fixtures
		$userArr = $this->getConnection()
			->createQueryTable('user_id', 'SELECT * FROM v_user WHERE userId IN (SELECT userId FROM BillingProfile) AND isActive=1')
			->getRow(0);

        $user = (new User())
            ->setId($userArr['userId']);
		$creditCardArr = $this->getConnection()
			->createQueryTable('credit_card', 'SELECT * FROM v_user_payment_profile WHERE userId NOT IN (' . $user->getId() . ') LIMIT 1')
			->getRow(0);

		$creditCard = (new CreditCard())
			->setId($creditCardArr['id']);

		// exercise SUT
		$this->transactionManager->chargeCreditCard($user, $creditCard, 20.00, Transaction::DESC_REFERRAL);
	}
	
	public function testChargeCreditCardSavesTransactionOnSuccess()
	{
	    $this->markTestIncomplete();
//		// set up fixtures
//		$userId = $this->getConnection()
//		               ->createQueryTable('user_id', 'SELECT * FROM v_user WHERE userId IN (SELECT userId FROM BillingProfile) AND isActive=1')
//		               ->getRow(0)['userId'];
//
//		$creditCardArr = $this->getConnection()
//		                      ->createQueryTable('credit_card', 'SELECT * FROM v_user_payment_profile WHERE userId NOT IN (' . $userId . ') LIMIT 1')
//		                      ->getRow(0);
//
//		$creditCard = (new CreditCard())
//			->setId($creditCardArr['id']);
//
//		// exercise SUT
//		$this->transactionManager->chargeCreditCard($userId, $creditCard, 20.00, Transaction::DESC_REFERRAL);
//
//		// make assertions
	}

	public function testChargeCreditCardSavesTransactionOnFailure()
	{
		$this->markTestIncomplete();
	}
}