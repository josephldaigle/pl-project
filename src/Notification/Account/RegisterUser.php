<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/30/18
 * Time: 2:50 PM
 */

namespace PapaLocal\Notification\Account;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class RegisterUser
 *
 * Model the notification sent to new users upon successful registration.
 *
 * @package PapaLocal\Notification\Account
 */
class RegisterUser extends AbstractNotification implements EmailStrategyInterface
{
	/**
	 * @var string
	 */
	private $recipient;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var array
	 */
	private $templateArgs;

	/**
	 * RegisterUser constructor.
	 *
	 * @param string $recipient
	 * @param array  $templateArgs
	 */
	public function __construct(string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'Welcome to ' . Ewebify::APP_NAME . '!';
		$this->messageTemplate = 'We are so happy you signed up, and you\'ll see why soon!';

		// set message body args
		$this->messageBodyArgs = array();

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = 'Welcome to ' . Ewebify::APP_NAME . '!';
	}

	/**
	 * @inheritdoc
	 */
	protected function getConfiguredStrategies(): array
	{
		return [self::STRATEGY_APP, self::STRATEGY_EMAIL];
	}

	/**
	 * @inheritdoc
	 */
	public function getRecipient(): string
	{
		return $this->recipient;
	}

	/**
	 * @inheritdoc
	 */
	public function getSubject(): string
	{
		return $this->subject;
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateName(): string
	{
		return 'emails/account/newUser.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}