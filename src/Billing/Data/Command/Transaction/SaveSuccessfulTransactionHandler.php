<?php
/**
 * Created by Joseph Daigle.
 * Date: 1/1/19
 * Time: 10:07 PM
 */


namespace PapaLocal\Billing\Data\Command\Transaction;


use PapaLocal\Core\Data\AdaptedTableGateway;


/**
 * Class SaveSuccessfulTransactionHandler.
 *
 * @package PapaLocal\Billing\Data\Command\Transaction
 */
class SaveSuccessfulTransactionHandler
{
    /**
     * @var AdaptedTableGateway
     */
    private $tableGateway;

    /**
     * SaveSuccessfulTransactionHandler constructor.
     *
     * @param AdaptedTableGateway $tableGateway
     */
    public function __construct(AdaptedTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(SaveSuccessfulTransaction $command)
    {
        $row = array(
            'guid' => $command->getGuid(),
            'billingProfileId' => $command->getBillingProfileId(),
            'userId' => $command->getUserId(),
            'description' => $command->getDescription(),
            'amount' => $command->getAmount(),
            'type' => $command->getType(),
            'transactionId' => $command->getTransactionId(),
            'referralId' => $command->getReferralId(),
            'payMethodId' => $command->getPayMethodId(),
        );

        $this->tableGateway->setTable('JournalSuccess');
        $this->tableGateway->create($row);

        return;
    }
}