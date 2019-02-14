<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/9/18
 * Time: 7:53 PM
 */

namespace PapaLocal\Data\Command\Company;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\PhoneNumber;

/**
 * UpdatePhoneNumber.
 *
 * @package PapaLocal\Data\Command\Company
 */
class UpdatePhoneNumber extends QueryCommand
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
     * UpdatePhone constructor.
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
        // start transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            $this->tableGateway->setTable('L_PhoneNumberType');
            $typeRows = $this->tableGateway->findBy('description', $this->phoneNumber->getType());

            if (count($typeRows) < 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find phone type: %s',
                    $this->phoneNumber->getType()));
            }

            $this->tableGateway->setTable('PhoneNumber');
            $phoneRows = $this->tableGateway->findBy('phoneNumber', $this->phoneNumber->getPhoneNumber());

            if (count($phoneRows) !== 1) {
                $phoneId = $this->tableGateway->create(array('phoneNumber' => $this->phoneNumber->getPhoneNumber()));
            } else {
                $phoneId = $phoneRows[0]['id'];
            }

            $this->tableGateway->setTable('R_CompanyPhoneNumber');
            $compPhoneRows = $this->tableGateway->findByColumns(array(
                'companyId' => $this->companyId,
                'typeId' => $typeRows[0]['id']
            ));

            if (count($compPhoneRows) !== 1) {
                throw new QueryCommandFailedException(
                    sprintf('Unexpected row count returned for R_CompanyPhoneNumber: %s, company id: %s, count: %s',
                        $this->phoneNumber->getType(), $this->companyId, count($compPhoneRows)));
            }

            $compPhoneRows[0]['phoneId'] = $phoneId;

            $result = $this->tableGateway->update($compPhoneRows[0]);

            // commit transaction
            $this->tableGateway->connection->commit();

            return $result;
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