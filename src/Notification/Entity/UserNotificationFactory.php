<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\Entity;


use PapaLocal\Feed\Enum\FeedItemType;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Collection\Collection;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UserNotificationFactory.
 *
 * @package PapaLocal\Notification\Entity
 */
class UserNotificationFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * UserNotificationFactory constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param RecordInterface $record
     *
     * @return UserNotification
     */
    public function createFromRecord(RecordInterface $record): UserNotification
    {
        $notification = $this->serializer->denormalize([
            'guid' => $this->serializer->denormalize(['value' => $record['guid']], Guid::class, 'array'),
            'userGuid' => $this->serializer->denormalize(['value' => $record['userGuid']], Guid::class, 'array'),
            'title' => $record['title'],
            'message' => (isset($record['message'])) ? $record['message'] : '',
            'timeSent' => $record['timeSent'],
            'isRead' => (bool) $record['isRead'],
            'isDismissed' => (bool) $record['isDismissed']
        ], UserNotification::class, 'array');

        if (isset($record['associateItemGuid']) && ! empty($record['associateItemGuid'])) {
            $associateItem = $this->serializer->denormalize([
                'guid' => ['value' => $record['associateItemGuid']],
                'type' => ['value' => $record['associateItemType']]
            ], AssociateFeedItem::class, 'array');

            $notification->setAssociateFeedItem($associateItem);
        }

        return $notification;
    }


    /**
     * @param RecordSetInterface $recordSet
     *
     * @return Collection
     */
    public function createFromRecordSet(RecordSetInterface $recordSet): Collection
    {
        $userNotificationList = $this->serializer->denormalize([], Collection::class, 'array');

        foreach ($recordSet as $record) {
            $userNotification = $this->createFromRecord($record);
            $userNotificationList->add($userNotification);
        }

        return $userNotificationList;
    }
}