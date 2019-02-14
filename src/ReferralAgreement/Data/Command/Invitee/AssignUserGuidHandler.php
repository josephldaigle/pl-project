<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 11:07 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class AssignUserGuidHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class AssignUserGuidHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * AssignUserGuidHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param AssignUserGuid $command
     *
     * @throws \Exception
     */
    function __invoke(AssignUserGuid $command)
    {
        try {
            $this->tableGateway->startTransaction();

            // find the record(s) to update
            $this->tableGateway->setTable('ReferralAgreementInvitee');
            $records = $this->tableGateway->findBy('emailAddress', $command->getEmailAddress());

            if (count($records) < 1) {
                throw new CommandException(sprintf('Cannot find an invitee with emailAddress = %s.',
                    $command->getEmailAddress()), CommandExceptionCode::NOT_FOUND());
            }

            // set user guid on row
            foreach ($records as $record)
            {
                $record['userGuid'] = $command->getUserGuid();

                // save changes
                $this->tableGateway->update($record->properties());
            }

            $this->tableGateway->commitTransaction();

        } catch (\Exception $exception) {
            $this->tableGateway->rollbackTransaction();
            throw $exception;
        }

        return;
    }

}