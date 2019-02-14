<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/23/18
 * Time: 5:04 PM
 */


namespace PapaLocal\Billing\Data;


use PapaLocal\Billing\Data\Command\Transaction\SaveSuccessfulTransaction;
use PapaLocal\Billing\Data\Command\UpdateRechargeSetting;
use PapaLocal\Billing\Data\Query\FindByUserGuid;
use PapaLocal\Billing\ValueObject\RechargeSetting;
use PapaLocal\Billing\ValueObject\TransactionInterface;
use PapaLocal\Core\Data\AbstractMessageFactory;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class MessageFactory
 *
 * @package PapaLocal\Billing\Data
 */
class MessageFactory extends AbstractMessageFactory
{
    /**
     * @param GuidInterface        $guid
     * @param TransactionInterface $transaction
     *
     * @return SaveSuccessfulTransaction
     */
    public function newSaveSuccessfulTransaction(GuidInterface $guid, TransactionInterface $transaction)
    {
        return new SaveSuccessfulTransaction($guid, $transaction);
    }

    /**
     * @param GuidInterface   $userGuid
     * @param RechargeSetting $rechargeSetting
     *
     * @return UpdateRechargeSetting
     */
    public function newUpdateRechargeSetting(GuidInterface $userGuid, RechargeSetting $rechargeSetting): UpdateRechargeSetting
    {
        return new UpdateRechargeSetting($userGuid, $rechargeSetting);
    }

    /**
     * @deprecated use newLoadUserBillingProfile
     *
     * @param string $userId
     *
     * @return FindByUserGuid
     */
    public function newFindByUserGuid(string $userId): FindByUserGuid
    {
        return new FindByUserGuid($userId);
    }
}