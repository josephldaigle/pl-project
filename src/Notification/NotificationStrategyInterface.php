<?php
/**
 * Created by Joseph Daigle.
 * Date: 4/20/18
 * Time: 9:54 PM
 */

namespace PapaLocal\Notification;


/**
 * Interface NotificationStrategyInterface
 *
 * Describe a notification strategy.
 *
 * @package PapaLocal\Notification
 */
interface NotificationStrategyInterface
{
	/**
	 * Send a notification to a user within the application only.
	 *
	 * @param NotificationInterface $notification
	 *
	 * @return mixed
	 */
	public function send(NotificationInterface $notification);
}