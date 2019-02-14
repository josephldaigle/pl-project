<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/18/18
 * Time: 12:57 PM
 */


namespace Test\Functional\Notification;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Notification\Notifier;
use PapaLocal\Test\WebDatabaseTestCase;
use PapaLocal\Billing\Notification\AddPayMethod;
use PapaLocal\Billing\Notification\DeletePayMethod;


/**
 * Class NotifierTest
 *
 * @package Test\Functional\Notification
 */
class NotifierTest extends WebDatabaseTestCase
{
	/**
	 * @var Notifier
	 */
	private $notifier;

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
	    $this->configureDataSet([
            'Person',
            'User',
            'L_EmailAddressType',
            'EmailAddress',
            'R_PersonEmailAddress',
            'R_UserNotification'
        ]);

		parent::setUp();

		$this->notifier = $this->diContainer->get('PapaLocal\Notification\Notifier');
	}

	public function testSendSavesNotificationToDatabaseOnSuccess()
	{
		// set up fixtures
		$begNotTableRowCount = $this->getConnection()->getRowCount('R_UserNotification');

		$userId = $this->getConnection()
            ->createQueryTable('user_id', 'SELECT * FROM User WHERE isActive = 1 LIMIT 1')
			->getRow(0)['guid'];

        $guid = new Guid($userId);

		$notification = new DeletePayMethod(4742);

		// exercise SUT
		$this->notifier->sendUserNotification($guid, $notification);

		// make assertions
		$this->assertTableRowCount('R_UserNotification', $begNotTableRowCount + 1, 'unexpected table row count');
	}

	public function testCanSendEmailNotification()
	{
		// set up fixtures
		$begNotTableRowCount = $this->getConnection()->getRowCount('R_UserNotification');
        $userId = $this->getConnection()
                       ->createQueryTable('user_id', 'SELECT * FROM User WHERE isActive = 1 LIMIT 1')
                       ->getRow(0)['guid'];

        $guid = new Guid($userId);

		$notification = new AddPayMethod(4725, 'joe@ewebify.com', array(
			'accountNumber' => 4725,
			'cardholder' => 'Joseph Daigle',
			'expirationDate' => '12/22'
		));

		// exercise SUT
		$this->notifier->sendUserNotification($guid, $notification);

		// make assertions
		$this->assertTableRowCount('R_UserNotification', $begNotTableRowCount + 1, 'unexpected table row count');
	}

}