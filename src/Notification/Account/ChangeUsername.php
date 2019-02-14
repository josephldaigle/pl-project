<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/2/18
 * Time: 10:09 AM
 */


namespace PapaLocal\Notification\Account;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ChangeUsername
 *
 * Model the notification sent to a user when their username is changed.
 *
 * @package PapaLocal\Notification\Account
 */
class ChangeUsername extends AbstractNotification implements EmailStrategyInterface
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
	 * ChangeUsername constructor.
	 *
	 * @param string $recipient
	 * @param array  $templateArgs
	 */
	public function __construct(string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'Your username has changed.';
		$this->messageTemplate = 'If you did not recently attempt to change your username, please contact us immediately.';

		// set message body args
		$this->messageBodyArgs = array();

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = 'Your ' . Ewebify::APP_NAME . ' account username has changed.';
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
		return 'emails/account/changeUsername.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}