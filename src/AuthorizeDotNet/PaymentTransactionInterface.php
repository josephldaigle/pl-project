<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/14/18
 * Time: 5:20 AM
 */

namespace PapaLocal\AuthorizeDotNet;

use PapaLocal\Entity\Billing\CreditCardInterface;

/**
 * Interface PaymentTransactionInterface.
 *
 * Describe payment transactions in Authorize.Net.
 *
 * @package PapaLocal\AuthorizeDotNet
 */
interface PaymentTransactionInterface
{
    /**
     * @param string              $username
     * @param CreditCardInterface $creditCard
     * @param float               $amount
     *
     * @return mixed
     *
     * @throws \LogicException if the charge cannot be processed
     *
     * @see https://developer.authorize.net/api/reference/#payment-transactions-charge-a-credit-card
     */
    public function chargeCreditCard(string $username, CreditCardInterface $creditCard, float $amount);

    /**
     * @param string              $username
     * @param CreditCardInterface $creditCard
     * @param float               $amount
     *
     * @return mixed
     *
     * @see https://developer.authorize.net/api/reference/#payment-transactions-refund-a-transaction
     *
     * @throws \LogicException if the amount cannot be refunded
     */
    public function refundCreditCard(string $username, CreditCardInterface $creditCard, float $amount);
}