<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/14/18
 * Time: 5:27 PM
 */

namespace PapaLocal\AuthorizeDotNet;

use PapaLocal\Entity\Billing\CreditCardInterface;

/**
 * Interface PaymentMethodInterface.
 *
 * Describe interactions for a user payment profile.
 *
 * @package PapaLocal\AuthorizeDotNet
 */
interface PaymentMethodInterface
{
    /**
     * Update a customer's payment profile.
     *
     * @param int                 $authNetCustId
     * @param CreditCardInterface $creditCard
     * @return mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-create-customer-payment-profile
     */
    public function createCreditCardProfile(int $authNetCustId, CreditCardInterface $creditCard);

    /**
     * Delete a customer's payment profile.
     *
     * @param string              $username
     * @param CreditCardInterface $creditCard
     * @return mixed
     *
     * @see https://developer.authorize.net/api/reference/index.html#customer-profiles-delete-customer-profile
     */
    public function deleteCreditCardProfile(string $username, CreditCardInterface $creditCard);
}