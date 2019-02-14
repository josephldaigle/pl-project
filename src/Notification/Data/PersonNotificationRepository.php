<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/10/18
 */


namespace PapaLocal\Notification\Data;


use PapaLocal\Core\Notification\PersonNotification;
use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Exception\NotificationException;
use PapaLocal\Entity\Exception\ServiceOperationFailedException;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\Notification\NotificationInterface;


/**
 * Class PersonNotificationRepository.
 *
 * Provide data access to notifications sent to people.
 *
 * @package PapaLocal\Notification\Data
 */
class PersonNotificationRepository extends AbstractRepository
{
    /**
     * @param int                   $personId
     * @param NotificationInterface $notification
     * @return mixed
     * @throws NotificationException
     */
    public function saveNotification(int $personId, NotificationInterface $notification)
    {
        try {
            $this->tableGateway->setTable('PersonNotification');
            $rowId = $this->tableGateway->create(array(
                'guid' => $notification->getGuid()->value(),
                'personId' => $personId,
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage()
            ));

            $newRows = $this->tableGateway->findById($rowId);
            if (count($newRows) < 1) {
                throw new NotificationException('Cannot find saved row.');
            }

            return $this->serializer->denormalize($newRows[0], PersonNotification::class, 'array');

        } catch (\Exception $exception) {
            throw new NotificationException(sprintf('Unable to save notification to database for user id %s: %s',
                    $personId,
                    $notification->getTitle())
                , $exception->getCode(), $exception);
        }
    }

    /**
     * @param PersonNotification $notification
     * @return int
     * @throws ServiceOperationFailedException
     */
    public function markRead(PersonNotification $notification)
    {
        $this->tableGateway->setTable('PersonNotification');
        $rows = $this->tableGateway->findById($notification->getId());

        if (count($rows) < 1) {
            throw new ServiceOperationFailedException(
                sprintf('The notification id %s, supplied to %s, could not be found.', $notification->getId(), __METHOD__));
        }

        $row = $rows[0];
        $row['isRead'] = 1;

        return $this->tableGateway->update($row);
    }


    /**
     * @param int $personId
     * @return mixed
     */
    public function loadNotifications(int $personId)
    {
        $this->tableGateway->setTable('PersonNotification');
        $rows = $this->tableGateway->findBy('personId', $personId);

        $notificationList = $this->serializer->denormalize(array(), NotificationList::class, 'array');

        foreach($rows as $row) {
            $notification = $this->serializer->denormalize(array(
                'id' => $row['id'],
                'guid' => array('value' => $row[0]['guid']),
                'personId' => $row['personId'],
                'title' => $row['title'],
                'message' => $row['message'],
                'timeSent' => $row['timeSent'],
                'isRead' => $row['isRead']
            ), PersonNotification::class, 'array');

            $notificationList->add($notification);
        }

        return $notificationList;
    }

    /**
     * Load a PersonNotification by it's guid.
     *
     * @param string $guid
     *
     * @return mixed
     */
    public function loadNotificationByGuid(string $guid)
    {
        $this->tableGateway->setTable('PersonNotification');
        $rows = $this->tableGateway->findBy('guid', $guid);

        $notification = $this->serializer->denormalize($rows[0], PersonNotification::class, 'array');
        $notification->setGuid(new Guid($rows[0]['guid']));

        return $notification;
    }

}