<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/17/18
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Notification\AbstractNotification;
use PapaLocal\Notification\EmailStrategyInterface;


/**
 * Class ChangePrimaryPayMethod.
 *
 * Model the notification sent to a user when their primary payment account is changed.
 *
 * @package PapaLocal\Billing\Notification
 */
class ChangePrimaryPayMethod extends AbstractNotification implements EmailStrategyInterface
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
     * @var array arguments to be used in the email template
     */
    private $templateArgs;

    /**
     * NewPrimaryPayMethod constructor.
     *
     * @param string $cardType
     * @param int    $cardNumber
     * @param string $recipient
     * @param array  $templateArgs
     */
    public function __construct(string $cardType, int $cardNumber, string $recipient, array $templateArgs)
    {
        $this->title = 'Primary Pay Method Changed.';
        $this->messageTemplate = 'The primary payment method for your account has been changed from to your %s account ending with %s.';
        $this->messageBodyArgs = array($cardType, $cardNumber);
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return array(self::STRATEGY_APP);
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
        return 'billing/changePrimaryPayMethod.html.twig';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateArgs(): array
    {
        return $this->templateArgs;
    }

}