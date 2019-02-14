<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/30/18
 * Time: 1:33 PM
 */

namespace PapaLocal\Notification\Account;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ChangePassword
 *
 * @package PapaLocal\Notification\Account
 *
 * Sent to a user when a failed attempt to change their password is made.
 */
class ChangePassword extends AbstractNotification implements EmailStrategyInterface
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
	 * ChangePasswordSuccess constructor.
	 *
	 * @param string $recipient
	 * @param array  $templateArgs
	 */
	public function __construct(string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'Your password has changed.';
		$this->messageTemplate = 'If you did not recently attempt to change your password, please contact us immediately.';

		// set message body args
		$this->messageBodyArgs = array();

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = 'Your ' . Ewebify::APP_NAME . ' account password has changed.';
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
		return 'emails/account/changePassword.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}