<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/18/18
 * Time: 11:46 AM
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * ManualDepositSuccess.
 *
 * Model the notification sent when a user makes a manual deposit.
 *
 * @package PapaLocal\Billing\Notification
 */
class ManualDepositSuccess extends AbstractNotification implements EmailStrategyInterface
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
     * ManualDepositSuccess constructor.
     *
     * @param float  $depositAmount
     * @param float  $accountBalance
     * @param string $recipient
     * @param array  $templateArgs
     */
	public function __construct(float $depositAmount, float $accountBalance, string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'A manual deposit was successfully processed.';
		$this->messageTemplate = 'Manual deposit of $%0.2f was added to your account. Your account balance is $%0.2f.';

		// set message body args
		$this->messageBodyArgs = array($depositAmount, $accountBalance);

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = sprintf('A deposit has been made to your %s account. Check out your new balance.', Ewebify::APP_NAME);
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
		return 'emails/billing/manualDepositSuccess.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}

}