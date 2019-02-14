<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/1/18
 * Time: 8:56 AM
 */


namespace PapaLocal\Notification;


use PapaLocal\Billing\Notification\ManualDepositSuccess;


/**
 * TODO: Move to Billing/Notification namespace, and move configuration from notification.yaml to billing.yaml
 * Class NotificationFactory
 *
 * @package PapaLocal\Notification
 */
class NotificationFactory
{
    /**
     * Create an instance of ManualDepositSuccess.
     *
     * @param float  $depositAmount
     * @param float  $accountBalance
     * @param string $recipient
     * @param array  $templateArgs
     *
     * @return ManualDepositSuccess
     */
    public function createManualDepositSuccess(float $depositAmount, float $accountBalance, string $recipient, array $templateArgs)
    {
        return new ManualDepositSuccess($depositAmount, $accountBalance, $recipient, $templateArgs);
    }
}