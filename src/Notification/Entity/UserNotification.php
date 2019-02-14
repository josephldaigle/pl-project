<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */


namespace PapaLocal\Notification\Entity;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;


/**
 * Class UserNotification.
 *
 * @package PapaLocal\Notification\Entity
 */
class UserNotification
{
    /**
     * @var Guid
     */
    private $guid;

    /**
     * @var Guid
     */
    private $userGuid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $timeSent;

    /**
     * @var bool
     */
    private $isRead;

    /**
     * @var bool
     */
    private $isDismissed;

    /**
     * @var AssociateFeedItem
     */
    private $associateFeedItem;

    /**
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface
    {
        return $this->guid;
    }

    /**
     * @param Guid $guid
     */
    public function setGuid(Guid $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return Guid
     */
    public function getUserGuid(): Guid
    {
        return $this->userGuid;
    }

    /**
     * @param Guid $userGuid
     */
    public function setUserGuid(Guid $userGuid): void
    {
        $this->userGuid = $userGuid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTimeSent(): string
    {
        return $this->timeSent;
    }

    /**
     * @param string $timeSent
     */
    public function setTimeSent(string $timeSent): void
    {
        $this->timeSent = $timeSent;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @param bool $isRead
     */
    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }

    /**
     * @return bool
     */
    public function isDismissed(): bool
    {
        return $this->isDismissed;
    }

    /**
     * @param bool $isDismissed
     */
    public function setIsDismissed(bool $isDismissed): void
    {
        $this->isDismissed = $isDismissed;
    }

    /**
     * @return mixed
     */
    public function getAssociateFeedItem()
    {
        return $this->associateFeedItem;
    }

    /**
     * @param AssociateFeedItem $associateFeedItem
     */
    public function setAssociateFeedItem(AssociateFeedItem $associateFeedItem)
    {
        $this->associateFeedItem = $associateFeedItem;
    }
}