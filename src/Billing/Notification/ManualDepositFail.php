<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/19/18
 * Time: 9:04 AM
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ManualDepositFail
 *
 * Model the notification sent when a user's manual deposit attempt fails.
 *
 * TODO: Should we be sending this?
 *
 * @package PapaLocal\Billing\Notification
 */
class ManualDepositFail extends AbstractNotification implements EmailStrategyInterface
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
	 * ManualDepositFail constructor.
	 *
	 * @param float  $depositAmount
	 * @param float  $accountBalance
	 * @param string $recipient
	 * @param array  $templateArgs
	 */
	public function __construct(float $depositAmount, float $accountBalance, string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'A manual deposit attempt has failed.';
		$this->messageTemplate = 'An attempt to deposit %0.2f to your %s account has failed. Your account balance is %0.2f.';

		// set message body args
		$this->messageBodyArgs = array($depositAmount, Ewebify::APP_NAME, $accountBalance);

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = sprintf('Attention Required: Failed deposit attempt to your %s account.', Ewebify::APP_NAME);
	}

	/**
	 * @inheritdoc
	 */
	protected function getConfiguredStrategies(): array
	{
		return array(self::STRATEGY_APP, self::STRATEGY_EMAIL);
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
		return 'billing/manualDepositFail.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}