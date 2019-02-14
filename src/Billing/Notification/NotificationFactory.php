<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/16/19
 * Time: 4:20 PM
 */

namespace PapaLocal\Billing\Notification;


use PapaLocal\Billing\Notification\Message\ChargeSuccess;
use PapaLocal\Billing\Notification\Message\PayoutSuccess;
use PapaLocal\Billing\Notification\Message\RefundSuccess;


/**
 * Class NotificationFactory
 * @package PapaLocal\Billing\Notification
 */
class NotificationFactory
{
    /**
     * @param float $payoutAmount
     * @param float $availableBalance
     * @param string $recipient
     * @param array $templateArgs
     * @return PayoutSuccess
     */
    public function newPayoutSuccess(float $payoutAmount, float $availableBalance, string $recipient, array $templateArgs)
    {
        return new PayoutSuccess($payoutAmount, $availableBalance, $recipient, $templateArgs);
    }

    /**
     * @param float $chargeAmount
     * @param string $recipient
     * @param array $templateArgs
     * @return ChargeSuccess
     */
    public function newChargeSuccess(float $chargeAmount, string $recipient, array $templateArgs)
    {
        return new ChargeSuccess($chargeAmount, $recipient, $templateArgs);
    }

    /**
     * @param float $refundAmount
     * @param string $recipient
     * @param array $templateArgs
     * @return RefundSuccess
     */
    public function newRefundSuccess(float $refundAmount, string $recipient, array $templateArgs)
    {
        return new RefundSuccess($refundAmount, $recipient, $templateArgs);
    }
}