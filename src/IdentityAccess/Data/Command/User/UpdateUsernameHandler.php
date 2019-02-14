<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 10:15 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateUsernameHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateUsernameHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateUsernameHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateUsername $command)
    {
        // user record
        $this->tableGateway->setTable('v_user');
        $userRecords = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        // load email type
        $this->tableGateway->setTable('L_EmailAddressType');
        $emailTypeRows = $this->tableGateway->findBy('description', 'Username');

        // check if username exists
        $this->tableGateway->setTable('EmailAddress');
        $emailRows = $this->tableGateway->findBy('emailAddress', $command->getUsername());

        $emailId = null;
        if (! $emailRows->count() > 0) {
            // new username does not yet exist
            $this->tableGateway->create(array('emailAddress' => $command->getUsername()));

            $newEmailRows = $this->tableGateway->findBy('emailAddress', $command->getUsername());
            if (! $newEmailRows->count() > 0) {
                // email was not created
                throw new CommandException(sprintf('Unable to save username %s for user %s.', $command->getUsername(), $command->getUserGuid()));
            }

            $emailId = $newEmailRows->current()['id'];

        } else {
            $emailId = $emailRows->current()['id'];
        }

        // update username record
        $this->tableGateway->setTable('R_PersonEmailAddress');
        $usernameRows = $this->tableGateway->findByColumns(array(
            'personId' => $userRecords->current()['personId'],
            'typeId' => $emailTypeRows->current()['id']
        ));

        $usernameRow = $usernameRows->current();
        $usernameRow['emailId'] = $emailId;

        $this->tableGateway->update($usernameRow->properties());

        return;
    }

}