<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 11:23 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;


/**
 * Class SaveCompanyHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class SaveCompanyHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * SaveCompanyHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param SaveCompany $command
     */
    public function __invoke(SaveCompany $command)
    {
        $this->tableGateway->setTable('Company');
        $this->tableGateway->create(array(
            'guid' => $command->getCompanyGuid(),
            'name' => $command->getName()
        ));

        // fetch company
        $this->tableGateway->setTable('v_company');
        $companyRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

        // fetch role
        $this->tableGateway->setTable('L_UserRole');
        $roleRec = $this->tableGateway->findBy('name', SecurityRole::ROLE_COMPANY()->getValue())->current();

        // fetch user
        $this->tableGateway->setTable('v_user');
        $userRecord = $this->tableGateway->findBy('userGuid', $command->getOwnerUserGuid())->current();

        $this->tableGateway->setTable('R_UserCompanyRole');
        $this->tableGateway->create(array(
            'userId' => $userRecord['userId'],
            'companyId' => $companyRecord['id'],
            'roleId' => $roleRec['id']
        ));

        return;
    }
}