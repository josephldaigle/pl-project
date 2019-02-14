<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 8:30 PM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\PhoneNumber;

/**
 * CreateCompanyPhone.
 *
 * Creates a phone number for a company.
 */
class CreateCompanyPhone extends QueryCommand
{
    /**
     * @var int
     */
    private $companyId;

    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * CreateCompanyPhone constructor.
     *
     * @param int         $companyId
     * @param PhoneNumber $phoneNumber
     */
    public function __construct(int $companyId, PhoneNumber $phoneNumber)
    {
        $this->companyId = $companyId;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // start transactions
        $this->tableGateway->connection->beginTransaction();

        try {
            // create phone number
            $this->tableGateway->setTable('PhoneNumber');
            $phoneRows = $this->tableGateway->findBy('phoneNumber', $this->phoneNumber->getPhoneNumber());

            if (count($phoneRows) !== 1) {
                $phoneId = $this->tableGateway->create(array('phoneNumber' => $this->phoneNumber->getPhoneNumber()));
            } else {
                $phoneId = $phoneRows[0]['id'];
            }

            // fetch phone type id
            $this->tableGateway->setTable('L_PhoneNumberType');
            $phoneType = $this->tableGateway->findBy('description', $this->phoneNumber->getType());

            // create relationship
            $this->tableGateway->setTable('R_CompanyPhoneNumber');
            $this->tableGateway->create(array(
                'companyId' => $this->companyId,
                'phoneId' => $phoneId,
                'typeId' => $phoneType[0]['id']
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            $this->phoneNumber->setId($phoneId);
            return $this->phoneNumber;

        } catch (\Exception $exception) {
            // rollback transaction
            $this->tableGateway->connection->rollBack();

            throw $this->filterException($exception);
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