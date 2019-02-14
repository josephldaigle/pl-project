<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/7/18
 * Time: 4:03 PM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Data\AttrType;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\Exception\QueryException;

/**
 * CreateCompany.
 *
 * Creates a Company and assigns a User as it's administrator.
 */
class CreateCompany extends QueryCommand
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var Company
     */
    private $company;

    /**
     * Create constructor.
     *
     * @param int     $userId
     * @param Company $company
     */
    public function __construct(int $userId, Company $company)
    {
        $this->userId = $userId;
        $this->company = $company;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // begin transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            // create company
            $row = $this->serializer->normalize($this->company, 'array',
                array('attributes' => array('name', 'about', 'dateFounded')));

            $this->tableGateway->setTable('Company');
            $this->company->setId($this->tableGateway->create($row));

            // assign ROLE_COMPANY to user
            $this->tableGateway->setTable('L_UserRole');
            $roleId = $this->tableGateway->findBy('name', AttrType::SECURITY_ROLE_COMPANY);

            // save the company relationship
            $this->tableGateway->setTable('R_UserCompanyRole');
            $this->tableGateway->create(array(
                'userId' => $this->userId,
                'companyId' => $this->company->getId(),
                'roleId' => $roleId[0]['id']
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            return $this->company;

        } catch (\Exception $e) {
            // rollback transaction
            $this->tableGateway->connection->rollBack();

            throw $this->filterException($e);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}