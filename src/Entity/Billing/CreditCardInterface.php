<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/17/17
 * Time: 3:49 PM
 */

namespace PapaLocal\Entity\Billing;

use PapaLocal\Entity\AddressInterface;

/**
 * Interface CreditCardInterface.
 *
 * Describe a Credit Card.
 */
interface CreditCardInterface extends PaymentAccountInterface
{
    /**
     * Fetch the cardholder's first name.
     *
     * @return mixed
     */
    public function getFirstName();

    /**
     * Fetch the cardholder's last name.
     * @return mixed
     */
    public function getLastName();

    /**
     * Fetch the card number.
     *
     * Length varies by card provider.
     *
     * @return mixed
     */
    public function getCardNumber();

    /**
     * Fetch the card provider.
     *
     * @return mixed
     */
    public function getCardType();

    /**
     * Fetch the card's expiration date.
     *
     * @return mixed
     */
    public function getExpirationDate();

    /**
     * Fetch the card's security code (CVV).
     *
     * @return mixed
     */
    public function getSecurityCode();

    /**
     * Fetch the address associated with the card.
     *
     * @return AddressInterface
     */
    public function getAddress(): AddressInterface;

    /**
     * @return bool
     */
    public function isDefaultPayMethod(): bool;
}