<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 10:25 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException;


/**
 * Class UpdateAgreementStatusHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementStatusHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateAgreementStatusHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateAgreementStatus $command
     *
     * @throws AgreementNotFoundException
     * @throws CommandException
     */
    function __invoke(UpdateAgreementStatus $command)
    {
        // load the agreement for access to rowId (FK)
        $this->tableGateway->setTable('ReferralAgreement');
        $agmtRecord = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($agmtRecord->isEmpty()) {
            throw new AgreementNotFoundException(sprintf('Unable to find agreement with guid: %s.', $command->getAgreementGuid()));
        }

        // load the reason code
        $this->tableGateway->setTable('L_ReferralAgreementStatusReason');
        $reasonRecordSet = $this->tableGateway->findBy('reason', $command->getReason());

        if ($reasonRecordSet->count() < 1) {
            throw new CommandException(sprintf('Unable to find agreement reason: %s in L_ReferralAgreementStatusReason.', $command->getReason()), CommandExceptionCode::NOT_FOUND());
        }

        // load the user's id
        $this->tableGateway->setTable('v_user');
        $userRecords = $this->tableGateway->findBy('userGuid', $command->getAuthorGuid());

        if ($userRecords->count() < 1) {
            throw new CommandException(sprintf('Unable to find user with guid %s in v_user.', $command->getAuthorGuid()), CommandExceptionCode::NOT_FOUND());
        }

        // save the status change
        $this->tableGateway->setTable('ReferralAgreementStatus');
        $this->tableGateway->create(array(
            'agreementId' => $agmtRecord['id'],
            'status' => $command->getStatus(),
            'reasonId' => $reasonRecordSet->current()['id'],
            'updatedBy' => $userRecords->current()['userId']
        ));

        return;
    }

}