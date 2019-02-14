<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 8:55 PM
 */


namespace PapaLocal\Billing\Message;


use PapaLocal\Billing\Form\WithdrawFunds;
use PapaLocal\Billing\Message\Command\CreateBankAccount;
use PapaLocal\Billing\Message\Command\Transaction\ChargeAccount;
use PapaLocal\Billing\Message\Command\Transaction\DebitCreditCard;
use PapaLocal\Billing\Message\Command\Transaction\Payout;
use PapaLocal\Billing\Message\Command\Transaction\RefundAccount;
use PapaLocal\Billing\Message\Command\UpdateRechargeSetting;
use PapaLocal\Billing\Message\Query\LoadProfileForUser;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class MessageFactory
 *
 * Factory for creating messages related to the Billing domain.
 *
 * @package PapaLocal\Billing\Message
 */
class MessageFactory
{
    /**
     * @param string $userGuid
     * @param string $minBalance
     * @param string $maxBalance
     *
     * @return UpdateRechargeSetting
     */
    public function newUpdateRechargeSetting(string $userGuid, string $minBalance, string $maxBalance): UpdateRechargeSetting
    {
        return new UpdateRechargeSetting($userGuid, $minBalance, $maxBalance);
    }

    /**
     * @param string $userGuid
     *
     * @return LoadProfileForUser
     */
    public function newLoadUserBillingProfile(string $userGuid): LoadProfileForUser
    {
        return new LoadProfileForUser($userGuid);
    }

    /**
     * @param string $user
     * @param string $token
     *
     * @return CreateBankAccount
     */
    public function newCreateBankAccount(string $user, string $token): CreateBankAccount
    {
        return new CreateBankAccount($user, $token);
    }

    /**
     * @param WithdrawFunds $form
     * @param string $username
     *
     * @return Payout
     */
    public function newPayout(WithdrawFunds $form, string $username): Payout
    {
        return new Payout($form, $username);
    }

    /**
     * TODO: Implement DebitCreditCard
     *
     * @return DebitCreditCard
     */
    public function newDebitCreditCard(): DebitCreditCard
    {
        return new DebitCreditCard();
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $referralGuid
     *
     * @return ChargeAccount
     */
    public function newChargeAccount(GuidInterface $agreementGuid, GuidInterface $referralGuid): ChargeAccount
    {
        return new ChargeAccount($agreementGuid, $referralGuid);
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $referralGuid
     *
     * @return RefundAccount
     */
    public function newRefundAccount(GuidInterface $agreementGuid, GuidInterface $referralGuid): RefundAccount
    {
        return new RefundAccount($agreementGuid, $referralGuid);
    }
}