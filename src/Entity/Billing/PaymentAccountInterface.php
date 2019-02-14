<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/28/17
 */


namespace PapaLocal\Entity\Billing;


/**
 * Interface PaymentAccountInterface.
 *
 * Describe a payment method (Bank Account or Credit Card)
 */
interface PaymentAccountInterface
{
	/**
	 * Fetch the first name on the account.
	 *
	 * @return mixed
	 */
    public function getFirstName();

	/**
	 * Fetch the last name on the account.
	 * @return mixed
	 */
    public function getLastName();

	/**
	 * Fetch the customerId associated with the account.
	 *
	 * @return mixed
	 */
    public function getCustomerId();

    /**
     * Whether the account is a Credit Card or Bank Account.
     *
     * @return mixed
     */
    public function getPaymentType();
    public function getAccountNumber();
    public function getAddress();

    public function isDefaultPayMethod(): bool;
}