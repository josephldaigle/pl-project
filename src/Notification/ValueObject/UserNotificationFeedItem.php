<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\ValueObject;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\FeedItemInterface;
use PapaLocal\Notification\Entity\UserNotification;
use PapaLocal\Notification\NotificationInterface;


/**
 * Class UserNotificationFeedItem.
 *
 * @package PapaLocal\Notification\ValueObject
 */
class UserNotificationFeedItem implements FeedItemInterface
{
    /**
     * @var UserNotification
     */
    private $userNotification;

    /**
     * UserNotificationFeedItem constructor.
     *
     * @param UserNotification $userNotification
     */
    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

    /**
     * @inheritDoc
     */
    public function getGuid()
    {
        return $this->userNotification->getGuid();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->userNotification->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getTimeCreated(): string
    {
        return $this->userNotification->getTimeSent();
    }

    /**
     * @inheritDoc
     */
    public function getTimeUpdated(): string
    {
        return $this->userNotification->getTimeSent();
    }

    /**
     * @inheritDoc
     */
    public function getFeedType(): string
    {
        return 'notification';
    }

    /**
     * @inheritDoc
     */
    public function getCardBody(): string
    {
        return $this->userNotification->getMessage();
    }

    /**
     * @return string
     */
    public function getAssociateItemGuid(): string
    {
        return (is_null($this->userNotification->getAssociateFeedItem())) ? '' : $this->userNotification->getAssociateFeedItem()->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getAssociateItemType(): string
    {
        return (is_null($this->userNotification->getAssociateFeedItem())) ? '' : $this->userNotification->getAssociateFeedItem()->getType()->getValue();
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->userNotification->isRead();
    }

    /**
     * @return string
     */
    public function getTimeSent(): string
    {
        return $this->userNotification->getTimeSent();
    }
}