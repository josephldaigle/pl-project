<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/16/19
 * Time: 2:37 PM
 */

namespace PapaLocal\Billing\Message\Command\Transaction;


use PapaLocal\Billing\Form\WithdrawFunds;


/**
 * Class Payout
 * @package PapaLocal\Billing\Message\Command\Transaction
 */
class Payout
{
    /**
     * @var WithdrawFunds
     */
    private $form;

    /**
     * @var string
     */
    private $username;

    /**
     * Payout constructor.
     * @param WithdrawFunds $form
     * @param string $username
     */
    public function __construct(WithdrawFunds $form, string $username)
    {
        $this->form = $form;
    }

    /**
     * @return WithdrawFunds
     */
    public function getForm(): WithdrawFunds
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}