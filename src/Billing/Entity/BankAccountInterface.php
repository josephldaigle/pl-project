<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/19/17
 * Time: 8:17 PM
 */

namespace PapaLocal\Billing\Entity;


use PapaLocal\Entity\Billing\PaymentAccountInterface;


/**
 * Interface BankAccountInterface
 *
 * Describe a Bank Account.
 *
 * @package PapaLocal\Billing\Entity
 */
interface BankAccountInterface extends PaymentAccountInterface
{
    /**
     * @return string fetch the name on the account
     */
    public function getAccountHolder(): string;
    public function getBankName();
    public function getRoutingNumber();
    public function getAccountType();
}