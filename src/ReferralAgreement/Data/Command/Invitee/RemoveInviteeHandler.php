<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/6/19
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class RemoveInviteeHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class RemoveInviteeHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * RemoveInviteeHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param RemoveInvitee $command
     *
     * @throws CommandException
     */
    public function __invoke(RemoveInvitee $command)
    {
        $this->tableGateway->setTable('ReferralAgreementInvitee');
        $record = $this->tableGateway->findByGuid($command->getInviteeGuid());

        if ($record->isEmpty()) {
            throw new CommandException(sprintf('Cannot find an invitee with guid = %s.', $command->getInviteeGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $record['removed'] = 1;

        $this->tableGateway->update($record->properties());

        return;
    }

}