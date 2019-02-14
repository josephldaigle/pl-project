<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:41 AM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateFirstNameHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateFirstNameHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateFirstNameHandler constructor.
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
    public function __invoke(UpdateFirstName $command)
    {
        $this->tableGateway->setTable('v_user');
        $userRecs = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        if ($userRecs->count() > 0) {
            $this->tableGateway->setTable('Person');
            $personRecord = $this->tableGateway->findByGuid($userRecs->current()['personGuid']);

            $personRecord['firstName'] = $command->getFirstName();
            
            $this->tableGateway->update($personRecord->properties());
        }

        return;
    }
}