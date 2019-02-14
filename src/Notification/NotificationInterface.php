<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/20/18
 * Time: 9:15 PM
 */


namespace PapaLocal\Notification;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * NotificationInterface.
 *
 * Describes a notification.
 *
 * @package PapaLocal\Notification
 */
interface NotificationInterface
{
    /**
     * @param GuidInterface $guid
     *
     * @return mixed
     */
    public function setGuid(GuidInterface $guid);

    /**
     * Get the unique identifier.
     *
     * @return GuidInterface
     */
    public function getGuid(): GuidInterface;

	/**
	 * Get the notification title.
	 *
	 * @return mixed
	 */
	public function getTitle(): string;

	/**
	 * Get the notification message.
	 *
	 * @return mixed
	 */
	public function getMessage(): string;

	/**
	 * Get the strategies that have been configured for this notification.
	 *
	 * @return array
	 */
	public function getStrategies(): array;

    /**
     * Get the notification item guid.
     *
     * @return string
     */
    public function getAssociateItemGuid(): string;

    /**
     * Get the notification item type.
     *
     * @return string
     */
    public function getAssociateItemType(): string;
}