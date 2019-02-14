<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/10/18
 * Time: 9:58 AM
 */


namespace PapaLocal\Test;


use PapaLocal\Notification\AbstractNotification;


/**
 * NotificationDummyVO.
 *
 * @package PapaLocal\Test
 */
class NotificationDummyVO extends AbstractNotification
{
	/**
	 * NotificationDummyVO constructor.
	 *
	 * @param string $message
	 */
	public function __construct(string $message = 'This is the message body for the test dummy notification.')
	{
		$this->title = 'This is a test dummy notification.';
		$this->messageTemplate = $message;
	}

	/**
	 * @inheritdoc
	 */
	protected function getConfiguredStrategies(): array
	{
		return array(AbstractNotification::STRATEGY_APP, AbstractNotification::STRATEGY_SMS, AbstractNotification::STRATEGY_EMAIL);
	}
}