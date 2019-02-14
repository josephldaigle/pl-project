<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/10/18
 */


namespace PapaLocal\Core\Notification;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\FeedItemInterface;


/**
 * Class PersonNotification.
 *
 * @package PapaLocal\Core\Notification
 *
 * Model a PersonNotification entity.
 */
class PersonNotification implements FeedItemInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Guid
     */
    private $guid;

    /**
     * @var int
     */
    private $personId;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PersonNotification
     */
    public function setId(int $id): PersonNotification
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Guid
     */
    public function getGuid(): Guid
    {
        return $this->guid;
    }

    /**
     * @param Guid $guid
     *
     * @return PersonNotification
     */
    public function setGuid(Guid $guid): PersonNotification
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return int
     */
    public function getPersonId(): int
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     * @return PersonNotification
     */
    public function setPersonId(int $personId): PersonNotification
    {
        $this->personId = $personId;
        return $this;
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
     * @return PersonNotification
     */
    public function setTitle(string $title): PersonNotification
    {
        $this->title = $title;
        return $this;
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
     * @return PersonNotification
     */
    public function setMessage(string $message): PersonNotification
    {
        $this->message = $message;
        return $this;
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
     * @return PersonNotification
     */
    public function setTimeSent(string $timeSent): PersonNotification
    {
        $this->timeSent = $timeSent;
        return $this;
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
     * @return PersonNotification
     */
    public function setIsRead(bool $isRead): PersonNotification
    {
        $this->isRead = $isRead;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeCreated(): string
    {
        return $this->timeSent;
    }

    /**
     * @inheritdoc
     */
    public function getTimeUpdated(): string
    {
        return $this->timeSent;
    }

    /**
     * @inheritdoc
     */
    public function getFeedType(): string
    {
        return 'personNotification';
    }

    /**
     * @inheritdoc
     */
    public function getCardBody(): string
    {
        return $this->message;
    }
}