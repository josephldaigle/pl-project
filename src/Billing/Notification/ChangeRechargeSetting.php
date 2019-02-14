<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/29/18
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Billing\ValueObject\RechargeSetting;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ChangeRechargeSetting.
 *
 * Notification sent when a user changes his automatic recharge settings.
 *
 * @package PapaLocal\Billing\Notification
 */
class ChangeRechargeSetting extends AbstractNotification implements EmailStrategyInterface
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
	 * ChangeRechargeSetting constructor.
	 *
	 * @param RechargeSetting $rechargeSetting
	 * @param string          $recipient
	 * @param array           $templateArgs
	 */
	public function __construct(RechargeSetting $rechargeSetting, string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'Your automatic payment settings have been updated.';
		$this->messageTemplate = 'Your automatic payment settings were recently changed to refill your account to $%0.2f when you balance falls below $%0.2f';

		// set message body args
		$this->messageBodyArgs = array($rechargeSetting->getMaxBalance(), $rechargeSetting->getMinBalance());

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = 'Your automatic payment settings have been updated';
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
		return 'emails/billing/changeRechargeSetting.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}