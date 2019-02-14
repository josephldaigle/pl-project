<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 3:15 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;


/**
 * Class UpdateUserRolesHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateUserRolesHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateUserRolesHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateUserRoles $command
     *
     * @throws UserNotFoundException
     */
    public function __invoke(UpdateUserRoles $command)
    {
        // fetch user record
        $this->tableGateway->setTable('v_user');
        $userRecordSet = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        if ($userRecordSet->count() < 1) {
            throw new UserNotFoundException(sprintf('Unable to locate a user with guid: %s', $command->getUserGuid()));
        }
        $userId = $userRecordSet->current()['userId'];

        // fetch available role records
        $this->tableGateway->setTable('L_UserRole');
        $roleRecords = $this->tableGateway->findAllOrderedById();

        $this->tableGateway->setTable('R_UserApplicationRole');

        // delete existing roles
        $userRoleRecords = $this->tableGateway->findBy('userId', $userId);

        foreach($userRoleRecords as $record) {
            $this->tableGateway->delete($record['id']);
        }

        // add new roles
        foreach($command->getRoles() as $role)
        {
            $arr = iterator_to_array($roleRecords);
            foreach ($arr as $record) {
                if (strcmp($record['name'], $role) == 0) {
                    $this->tableGateway->create(array(
                        'userId' => $userId,
                        'roleId' => $record['id']
                    ));
                }
            }
        }
    }

}