<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/14/18
 * Time: 9:29 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class AcceptInvitationHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class AcceptInvitationHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * AcceptInvitationHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param AcceptInvitation $command
     *
     * @throws CommandException
     */
    function __invoke(AcceptInvitation $command)
    {
        $this->tableGateway->setTable('ReferralAgreement');
        $agmtRecord = $this->tableGateway->findByGuid($command->getAgreementGuid());

        $this->tableGateway->setTable('ReferralAgreementInvitee');
        $records = $this->tableGateway->findByColumns(array(
            'agreementId' => $agmtRecord['id'],
            'userGuid' => $command->getUserGuid(),
        ));

        if ($records->count() < 1) {
            throw new CommandException(sprintf('Cannot find an invitee with agreementId = %s and userGuid = %s', $command->getAgreementGuid(), $command->getUserGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $record = $records->current();
        $record['accepted'] = 1;

        $this->tableGateway->update($record->properties());

        return;
    }

}