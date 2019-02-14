<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/10/18
 * Time: 9:25 PM
 */

namespace PapaLocal\Data\Command\Company;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * UpdateEmailAddress.
 *
 * @package PapaLocal\Data\Command\Company
 */
class UpdateEmailAddress extends QueryCommand
{
    /**
     * @var int
     */
    private $companyId;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * UpdateEmailAddress constructor.
     *
     * @param int          $companyId
     * @param EmailAddress $emailAddress
     */
    public function __construct(int $companyId, EmailAddress $emailAddress)
    {
        $this->companyId = $companyId;
        $this->emailAddress = $emailAddress;
    }
    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // start transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            $this->tableGateway->setTable('L_EmailAddressType');
            $typeRows = $this->tableGateway->findBy('description', $this->emailAddress->getType());

            if (count($typeRows) !== 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find email type: %s',
                    $this->emailAddress->getType()));
            }

            $this->tableGateway->setTable('EmailAddress');
            $emailRows = $this->tableGateway->findBy('emailAddress', $this->emailAddress->getEmailAddress());

            if (count($emailRows) != 1) {
                $emailId = $this->tableGateway->create(array('emailAddress' => $this->emailAddress->getEmailAddress()));
            } else {
                $emailId = $emailRows[0]['id'];
            }

            $this->tableGateway->setTable('R_CompanyEmailAddress');
            $compEmailRows = $this->tableGateway->findByColumns(array(
                'companyId' => $this->companyId,
                'typeId' => $typeRows[0]['id']
            ));

            if (count($compEmailRows) !== 1) {
                throw new QueryCommandFailedException(
                    sprintf('Unexpected row count returned for R_CompanyEmailAddress: %s, company id: %s, count: %s',
                        $this->emailAddress->getType(), $this->companyId, count($compEmailRows)));
            }

            $compEmailRows[0]['emailAddressId'] = $emailId;

            $result = $this->tableGateway->update($compEmailRows[0]);

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