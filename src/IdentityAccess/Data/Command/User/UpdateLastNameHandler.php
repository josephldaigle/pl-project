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
 * Class UpdateLastNameHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateLastNameHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateLastNameHandler constructor.
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
    public function __invoke(UpdateLastName $command)
    {
        $this->tableGateway->setTable('v_user');
        $userRecs = $this->tableGateway->findBy('userGuid', $command->getUserGuid());

        if ($userRecs->count() > 0) {
            $this->tableGateway->setTable('Person');
            $personRecord = $this->tableGateway->findByGuid($userRecs->current()['personGuid']);

            $personRecord['lastName'] = $command->getLastName();
            $this->tableGateway->update($personRecord->properties());
        }

        return;
    }

}