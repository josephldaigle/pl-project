<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/10/18
 * Time: 10:15 AM
 */


namespace PapaLocal\Notification;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Notification\ValueObject\AssociateFeedItem;


/**
 * AbstractNotification.
 *
 * Base class for Notification(s).
 *
 * @package PapaLocal\Notification
 */
abstract class AbstractNotification implements NotificationInterface
{
    /**
     * @deprecated use PapaLocal\Notification\ValueObject\Strategy
     */
	public const STRATEGY_APP = '15b8a6913dc0cf5b8a6913dc0d1994367930';

    /**
     * @deprecated use PapaLocal\Notification\ValueObject\Strategy
     */
	public const STRATEGY_EMAIL = '15b8a6917abe4c5b8a6917abe4e452775858';

    /**
     * @deprecated use PapaLocal\Notification\ValueObject\Strategy
     */
	public const STRATEGY_SMS = '15b8a69188be7e5b8a69188be80328736519';

    /**
     * @var GuidInterface
     */
	protected $guid;

	/**
	 * The title of the notification.
	 * A brief summary, to be used in email subject, or app notification tray.
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * The message body, containing full details of the notification.
	 *
	 * @var string
	 */
	protected $messageTemplate;

	/**
	 * @var array arguments to be parsed into message body.
	 */
	protected $messageBodyArgs;

    /**
     * @var AssociateFeedItem
     */
	protected $associateFeedItem;

    /**
     * @param GuidInterface $guid
     *
     * @return AbstractNotification
     */
    public function setGuid(GuidInterface $guid): AbstractNotification
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface
    {
        return $this->guid;
    }

	/**
	 * @inheritdoc
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @inheritdoc
	 */
	public function getMessage(): string
	{
		return ($this->messageBodyArgs === null) ? $this->messageTemplate : vsprintf($this->messageTemplate, $this->messageBodyArgs);
	}

	/**
	 * Should be one of the defined constants in this class.
	 *
	 * @return array
	 */
	protected abstract function getConfiguredStrategies(): array;

	/**
	 * Fetch the defined strategies that can be configured when writing new notification instance classes.
	 *
	 * @return array
	 */
	protected function getAvailableStrategies(): array
	{
		return array(self::STRATEGY_APP, self::STRATEGY_EMAIL, self::STRATEGY_SMS);
	}

    /**
     * Get a list of the defined strategies.
     *
     * @return array
     * @throws \LogicException
     */
	public function getStrategies(): array
	{
		foreach ($this->getConfiguredStrategies() as $strategy) {
			if (! in_array($strategy, $this->getAvailableStrategies())) {
				throw new \LogicException(sprintf('The strategy % is not allowed in %s', $strategy, get_class($this)));
			}

			return $this->getConfiguredStrategies();
		}
	}

    /**
     * @inheritDoc
     */
    public function getAssociateItemGuid(): string
    {
        return (is_null($this->associateFeedItem)) ? '' : $this->associateFeedItem->getGuid()->value();
    }

    /**
     * @inheritDoc
     */
    public function getAssociateItemType(): string
    {
        return (is_null($this->associateFeedItem)) ? '' : $this->associateFeedItem->getType()->getValue();

    }
}