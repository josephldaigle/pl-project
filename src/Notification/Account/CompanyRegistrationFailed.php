<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/11/18
 * Time: 9:39 AM
 */

namespace PapaLocal\Notification\Account;


use PapaLocal\Notification\AbstractNotification;


/**
 * Class CompanyRegistrationFailed
 *
 * Models the notification sent to a user when he registered successfully, but the
 * company data provided could not be saved.
 *
 * @package PapaLocal\Notification\Account
 */
class CompanyRegistrationFailed extends AbstractNotification
{
	/**
	 * CompanyRegistrationFailed constructor.
	 *
	 * @param string $recipient
	 */
	public function __construct(string $recipient, $url)
	{
		// set the title and message
		$this->title = 'There was a problem during your registration.';
		$this->messageTemplate = "It looks like we ran into a problem registering your business information. You can add your company in your <a href=\"%s\">profile</a>.";

		// set message body args
		$this->messageBodyArgs = array(preg_replace('/(http:\/\/)/', 'https://', $url) . '/account/profile');
	}

	/**
	 * @inheritdoc
	 */
	protected function getConfiguredStrategies(): array
	{
		return [self::STRATEGY_APP];
	}
}