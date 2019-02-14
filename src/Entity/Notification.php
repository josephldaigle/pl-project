<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/20/18
 * Time: 10:07 PM
 */


namespace PapaLocal\Entity;


use PapaLocal\Core\ValueObject\Guid;


/**
 * Notification.
 *
 * @package PapaLocal\Entity
 */
class Notification implements FeedItemInterface
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
	private $userId;

    /**
     * @var string
     */
	private $title;

    /**
     * @var string
     */
	private $message;

    /**
     * @var bool
     */
    private $isRead;

	/**
	 * @var bool
	 */
    private $isDismissed;

    /**
     * @var string
     */
	private $timeSent;

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

	/**
	 * @param int $id
	 *
	 * @return Notification
	 */
    public function setId(int $id): Notification
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
     * @return Notification
     */
    public function setGuid(Guid $guid): Notification
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getMessage()
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
     * @return mixed
     */
    public function isRead()
    {
        return ($this->isRead) ? true : false;
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
		return ($this->isDismissed) ? true : false;
	}

	/**
	 * @param bool $isDismissed
	 *
	 * @return Notification
	 */
	public function setIsDismissed(bool $isDismissed): Notification
	{
		$this->isDismissed = $isDismissed;

		return $this;
	}

    /**
     * @return mixed
     */
    public function getTimeSent()
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
	 * Determine if this notification was sent before $notification.
	 *
	 * @param Notification $notification
	 *
	 * @return bool
	 */
    public function sentBefore(Notification $notification)
    {
    	return date('Y-m-d H:i:s', $this->getTimeSent()) < date('Y-m-d H:i:s', $notification->getTimeSent());
    }

    /**
	 * @inheritdoc FeedItemInterface
	 */
	public function getTimeCreated(): string
	{
		return $this->timeSent;
	}

	/**
	 * @inheritdoc FeedItemInterface
	 */
	public function getTimeUpdated(): string
	{
		return $this->timeSent;
	}

	/**
	 * @inheritdoc FeedItemInterface
	 */
	public function getFeedType(): string
	{
		return 'notification';
	}

	/**
	 * @return string
	 */
	public function getCardBody(): string
	{
		return $this->message;
	}

}