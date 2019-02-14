<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/20/18
 * Time: 9:58 PM
 */


namespace PapaLocal\Notification\Data;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Exception\NotificationException;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\Entity\Notification;
use PapaLocal\Notification\Entity\UserNotification;
use PapaLocal\Notification\Entity\UserNotificationFactory;
use PapaLocal\Notification\NotificationInterface;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class NotificationRepository.
 *
 * @package PapaLocal\Notification\Data
 */
class NotificationRepository extends AbstractRepository
{
    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var UserNotificationFactory
     */
    private $userNotificationFactory;

    /**
     * NotificationRepository constructor.
     *
     * @param DataResourcePool        $dataResourcePool
     * @param MessageFactory          $mysqlMessageFactory
     * @param MessageBusInterface     $mysqlBus
     * @param UserNotificationFactory $userNotificationFactory
     */
    public function __construct(DataResourcePool $dataResourcePool,
                                MessageFactory $mysqlMessageFactory,
                                MessageBusInterface $mysqlBus,
                                UserNotificationFactory $userNotificationFactory)
    {
        parent::__construct($dataResourcePool);

        $this->mysqlMsgFactory = $mysqlMessageFactory;
        $this->mysqlBus = $mysqlBus;
        $this->userNotificationFactory = $userNotificationFactory;
    }

    /**
     * @param GuidInterface $notificationGuid
     *
     * @return UserNotification
     */
    public function findByGuid(GuidInterface $notificationGuid): UserNotification
    {
        $query = $this->mysqlMsgFactory->newFindByGuid('v_user_notification', $notificationGuid);
        $record = $this->mysqlBus->dispatch($query);

        return $this->userNotificationFactory->createFromRecord($record);
    }

	/**
	 * Saves a record a notification sent to a user.
	 *
	 * @param int                      $userId
	 * @param NotificationInterface $notification
	 *
	 * @return mixed
	 * @throws NotificationException
	 */
	public function saveNotification(int $userId, NotificationInterface $notification)
	{
		try {
			$this->tableGateway->setTable('R_UserNotification');
			$rowId = $this->tableGateway->create(array(
			    'guid' => $notification->getGuid()->value(),
				'userId' => $userId,
				'associateItemGuid' => $notification->getAssociateItemGuid(),
				'associateItemType' => $notification->getAssociateItemType(),
				'title' => $notification->getTitle(),
				'message' => $notification->getMessage()
			));

			$newRows = $this->tableGateway->findById($rowId);
			if (count($newRows) < 1) {
				throw new NotificationException('Cannot find saved row.');
			}

			return $this->serializer->denormalize($newRows[0], Notification::class, 'array');

		} catch (\Exception $exception) {
			throw new NotificationException(sprintf('Unable to save notification to database for user id %s: %s', $userId, $notification->getTitle()), $exception->getCode(), $exception);
		}
	}

    /**
     * @param GuidInterface         $userGuid
     * @param NotificationInterface $notification
     */
    public function save(GuidInterface $userGuid, NotificationInterface $notification)
    {
        $this->tableGateway->setTable('v_user');
        $userIdRows = $this->tableGateway->findBy('userGuid', $userGuid->value());

        $this->tableGateway->setTable('R_UserNotification');
        $this->tableGateway->create(array(
            'guid' => $notification->getGuid()->value(),
            'userId' => $userIdRows[0]['userId'],
            'associateItemGuid' => $notification->getAssociateItemGuid(),
            'associateItemType' => $notification->getAssociateItemType(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage()
        ));

        return;
    }

    /**
     * Mark a notification as 'read' by the user.
     *
     * @param GuidInterface $notificationGuid
     */
    public function markRead(GuidInterface $notificationGuid)
    {
        // fetch the notification to update
        $notificationQuery = $this->mysqlMsgFactory->newFindByGuid('R_UserNotification', $notificationGuid);
        $notificationRecord = $this->mysqlBus->dispatch($notificationQuery);

        if ($notificationRecord->isEmpty()) {
            return;
        }

        // update the notification
        $notificationRecord['isRead'] = 1;

        $this->tableGateway->setTable('R_UserNotification');
        $this->tableGateway->update($notificationRecord->properties());

        return;
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return NotificationList
     */
    public function getUserNotifications(GuidInterface $userGuid)
    {
        $query = $this->mysqlMsgFactory->newFindBy('v_user_notification', 'userGuid',  $userGuid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        $collection = $this->userNotificationFactory->createFromRecordSet($recordSet);
        $notificationList = new NotificationList($collection->all());

        return $notificationList;
    }

    /**
     * @param GuidInterface $guid
     *
     * @return Collection
     */
    public function findByUserGuid(GuidInterface $guid)
    {
        $query = $this->mysqlMsgFactory->newFindBy('v_user_notification', 'userGuid', $guid->value());
        $recordSet = $this->mysqlBus->dispatch($query);

        return $this->userNotificationFactory->createFromRecordSet($recordSet);
    }
}