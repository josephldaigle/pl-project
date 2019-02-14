<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/28/18
 * Time: 10:28 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;


/**
 * Class UpdatePasswordHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdatePasswordHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdatePasswordHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdatePassword $command
     *
     * @throws UserNotFoundException
     */
    public function __invoke(UpdatePassword $command)
    {
        $this->tableGateway->setTable('User');
        $userRow = $this->tableGateway->findByGuid($command->getUserGuid());

        if ($userRow->isEmpty()) {
            throw new UserNotFoundException(sprintf('Unable to locate a user with guid: %s', $command->getUserGuid()));
        }

        $userRow['password'] = $command->getPassword();

        $this->tableGateway->update($userRow->properties());

        return;
    }


}