<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 2:21 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class CreateUserHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class CreateUserHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * CreateUserHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param CreateUser $command
     */
    public function __invoke(CreateUser $command)
    {
        // save person
        $this->tableGateway->setTable('Person');
        $this->tableGateway->create(array(
            'guid' => $command->getPersonGuid(),
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName()
        ));
        $personRecord = $this->tableGateway->findByGuid($command->getPersonGuid());

        // save user
        $this->tableGateway->setTable('User');
        $this->tableGateway->create(array(
            'guid' => $command->getGuid(),
            'personId' => $personRecord['id'],
            'password' => $command->getPassword(),
            'timeZone' => 'America/New York'
        ));

        $this->tableGateway->setTable('L_EmailAddressType');
        $emailTypeRecSet = $this->tableGateway->findBy('description', 'Username');

        // save email address (username)
        $this->tableGateway->setTable('EmailAddress');
        $this->tableGateway->create(array(
            'emailAddress' => $command->getUsername()
        ));
        $emailRecSet = $this->tableGateway->findBy('emailAddress', $command->getUsername());

        $this->tableGateway->setTable('R_PersonEmailAddress');
        $this->tableGateway->create(array(
            'personId' => $personRecord['id'],
            'emailId' => $emailRecSet->current()['id'],
            'typeId' => $emailTypeRecSet->current()['id'],
        ));

        return;
    }
}