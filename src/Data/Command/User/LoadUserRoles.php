<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/6/17
 */

namespace PapaLocal\Data\Command\User;

use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\QueryCommand;

/**
 * Class LoadUserRoles.
 */
class LoadUserRoles extends QueryCommand
{

    /**
     * @var int
     */
    private $userId;

    /**
     * LoadUserRoles constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritdoc
     */
    protected function runQuery()
    {
        try {

           $this->tableGateway->setTable('v_user_roles');
           $rows = $this->tableGateway->findBy('userId', $this->userId);

           if (count($rows) < 1) {
               return false;
           }

	        $this->tableGateway->setTable('v_company_owner');
	        $coRoles = $this->tableGateway->findBy('userId', $this->userId);

	        if (count($coRoles) > 0) {
		        $rows = array_merge($rows, array(array('role' => AttrType::SECURITY_ROLE_COMPANY)));
	        }

           //filter out role names
           $roles = array();
           foreach($rows as $row) {
                $roles[] = $row['role'];
           }

           return $roles;

        } catch (\Exception $exception) {
            throw $this->filterException($exception);
        }
    }


    /**
     * @inheritdoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}