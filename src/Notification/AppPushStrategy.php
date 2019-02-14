<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/22/18
 * Time: 3:41 PM
 */


namespace PapaLocal\ValueObject\Notification;


use PapaLocal\Notification\NotificationInterface;
use PapaLocal\Notification\NotificationStrategyInterface;


/**
 * AppPushStrategy.
 *
 * @package PapaLocal\ValueObject\Notification
 */
class AppPushStrategy implements NotificationStrategyInterface
{
	/**
	 * @inheritDoc
	 */
	public function send(NotificationInterface $notification)
	{
		// TODO: Implement send() method.
	}
}