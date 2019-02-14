<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/4/18
 */


namespace Test\Functional\Notification\Data;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Notification;
use PapaLocal\Notification\Entity\UserNotification;
use PapaLocal\Test\NotificationDummyVO;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use PapaLocal\Test\WebDatabaseTestCase;


/**
 * Class NotificationRepositoryTest.
 *
 * @package Test\Functional\Notification\Data
 */
class NotificationRepositoryTest extends WebDatabaseTestCase
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->configureDataSet([
            'Person',
            'EmailAddress',
            'L_EmailAddressType',
            'R_PersonEmailAddress',
            'User',
            'L_UserRole',
            'R_UserApplicationRole',
            'R_UserNotification'
        ]);

        parent::setUp();

        $this->notificationRepository = $this->diContainer->get('PapaLocal\Core\Data\RepositoryRegistry')->get(NotificationRepository::class);
    }

	public function testSaveNotificationReturnsEntityOnSuccess()
	{
		// set up fixtures
		$userId = $this->getConnection()
		               ->createQueryTable('user_id', 'SELECT id from User WHERE isActive = 1 LIMIT 1')
		               ->getRow(0)['id'];

		$notification = new NotificationDummyVO();
		$guidMock = $this->createMock(Guid::class);
		$guidMock->method('value')
            ->willReturn('232f36bf-00a0-460c-8ef0-2b1956bc3930');

		$notification->setGuid($guidMock);

		// exercise SUT
		$result = $this->notificationRepository->saveNotification($userId, $notification);

		// make assertions
		$this->assertInstanceOf(Notification::class, $result, 'unexpected type');
		$this->assertSame($notification->getTitle(), $result->getTitle(), 'unexpected title');
		$this->assertSame($notification->getMessage(), $result->getMessage(), 'unexpected message');
    }

    public function testGetUserNotificationsReturnsNotificationListOnSuccess()
    {
        // set up fixtures
        $userGuid = $this->getConnection()
            ->createQueryTable('userGuid', 'SELECT userGuid FROM v_user_notification LIMIT 1')
            ->getRow(0)['userGuid'];

        // exercise SUT
        $result = $this->notificationRepository->getUserNotifications(new Guid($userGuid));

        // make assertions
        $this->assertInstanceOf(Collection::class, $result, 'unexpected type');
        $this->assertGreaterThan(0, $result->count(), 'unexpected count');

        foreach($result->all() as $notification) {
            $this->assertInstanceOf(UserNotification::class, $notification, 'unexpected class found in collection');

            $this->assertObjectHasAttribute('title', $notification, 'title not present');
            $this->assertNotNull($notification->getTitle(), 'title is null');

            $this->assertObjectHasAttribute('message', $notification, 'message not present');
            $this->assertNotNull($notification->getMessage(), 'message is null');

            $this->assertObjectHasAttribute('timeSent', $notification, 'timeSent not present');
            $this->assertNotNull($notification->getTimeSent(), 'timeSent is null');

            $this->assertObjectHasAttribute('isRead', $notification, 'isRead not present');
            $this->assertNotNull($notification->isRead(), 'isRead is null');

        }
    }

    public function testGetUserNotificationsReturnsEmptyNotificationListWhenUserHasNone()
    {
        // set up fixtures

        // exercise SUT
        $result = $this->notificationRepository->getUserNotifications(new Guid('c539e29a-f752-40e3-b361-a2ff47ae0cf2'));

        // make assertions
        $this->assertInstanceOf(Collection::class, $result, 'unexpected type');
        $this->assertEquals(0, $result->count(), 'unexpected count');
    }

    public function testMarkReadIsSuccess()
    {
        // set up fixtures
        $notificationId = $this->getConnection()
            ->createQueryTable('notification_id', 'SELECT guid FROM R_UserNotification WHERE isRead = 0 LIMIT 1')
            ->getRow(0)['guid'];

        // exercise SUT
        $this->notificationRepository->markRead(new Guid($notificationId));

        // fetch result data set
        $notificationRow = $this->getConnection()
            ->createQueryTable('notification', 'SELECT * FROM v_user_notification WHERE guid LIKE\'' . $notificationId . '\'')
            ->getRow(0);

        // make assertions
        $this->assertSame('1', $notificationRow['isRead']);
    }
}