<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/1/18
 * Time: 5:42 AM
 */


namespace PapaLocal\Billing\Event;


use PapaLocal\Entity\Billing\PaymentAccountInterface;
use PapaLocal\Entity\User;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class DepositCompletedEvent
 *
 * @package PapaLocal\Billing\Event
 */
class DepositCompletedEvent extends Event
{
    /**
     * @var User
     */
    private $recipient;

    /**
     * @var PaymentAccountInterface
     */
    private $paymentAccount;

    /**
     * @var float
     */
    private $depositAmount;

    /**
     * @var float
     */
    private $accountBalance;

    /**
     * DepositCompletedEvent constructor.
     *
     * @param User                    $recipient
     * @param PaymentAccountInterface $paymentAccount
     * @param float                   $depositAmount
     * @param float                   $accountBalance
     */
    public function __construct(
        User $recipient,
        PaymentAccountInterface $paymentAccount,
        float $depositAmount,
        float $accountBalance
    )
    {
        $this->recipient = $recipient;
        $this->paymentAccount = $paymentAccount;
        $this->depositAmount = $depositAmount;
        $this->accountBalance = $accountBalance;
    }

    /**
     * @return User
     */
    public function getRecipient(): User
    {
        return $this->recipient;
    }

    /**
     * @return PaymentAccountInterface
     */
    public function getPaymentAccount(): PaymentAccountInterface
    {
        return $this->paymentAccount;
    }

    /**
     * @return float
     */
    public function getDepositAmount(): float
    {
        return $this->depositAmount;
    }

    /**
     * @return float
     */
    public function getAccountBalance(): float
    {
        return $this->accountBalance;
    }
}