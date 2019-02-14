<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/17/18
 */


namespace PapaLocal\Billing\Notification;


use PapaLocal\Notification\AbstractNotification;


/**
 * Class DeletePayMethod.
 *
 * Model the notification sent to a user when a payment method is removed from their account.
 *
 * @package PapaLocal\Billing\Notification
 */
class DeletePayMethod extends AbstractNotification
{
    /**
     * DeletePayMethod constructor.
     *
     * @param int $cardNumber
     */
    public function __construct(int $cardNumber)
    {
        $this->title = 'A pay method has been removed.';
        $this->messageTemplate = 'Credit card ending in %s has been removed as a payment method from your account.';
        $this->messageBodyArgs = array($cardNumber);
    }

    /**
     * @inheritdoc
     */
    protected function getConfiguredStrategies(): array
    {
        return array(self::STRATEGY_APP);
    }
}