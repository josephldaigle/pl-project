<?php
/**
 * Created by Joseph Daigle.
 * Date: 5/10/18
 * Time: 10:18 AM
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Data\Ewebify;
use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * AddPayMethod.
 *
 * Model the notification sent when a user adds a new payment account.
 *
 * @package PapaLocal\Billing\Notification
 */
class AddPayMethod extends AbstractNotification implements EmailStrategyInterface
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
	 * @var string
	 */
	private $sender;

    /**
     * AddPayMethod constructor.
     *
     * @param string $cardNumber
     * @param string $recipient
     * @param array  $templateArgs
     */
	public function __construct(string $cardNumber, string $recipient, array $templateArgs)
	{
		// set the title and message
		$this->title = 'A new payment method was added.';
		$this->messageTemplate = 'Credit card ending in %s has been added as a payment method to your account.';

		// set message body args
		$this->messageBodyArgs = array($cardNumber);

		// set email config settings
		$this->recipient = $recipient;
		$this->templateArgs = $templateArgs;
		$this->subject = sprintf('A new credit card has been added to your %s account', Ewebify::APP_NAME);
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
		return 'emails/billing/addPayMethod.html.twig';
	}

	/**
	 * @inheritdoc
	 */
	public function getTemplateArgs(): array
	{
		return $this->templateArgs;
	}
}