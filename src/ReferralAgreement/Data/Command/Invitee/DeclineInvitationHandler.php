<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 7:49 AM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class DeclineInvitationHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class DeclineInvitationHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * DeclineInvitationHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param DeclineInvitation $command
     *
     * @throws CommandException
     */
    function __invoke(DeclineInvitation $command)
    {
        $this->tableGateway->setTable('ReferralAgreementInvitee');
        $record = $this->tableGateway->findByGuid($command->getInvitationGuid());

        if ($record->isEmpty()) {
            throw new CommandException(sprintf('Cannot find an invitee with guid = %s.', $command->getInvitationGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $record['declined'] = 1;

        $this->tableGateway->update($record->properties());

        return;
    }


}