<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/10/18
 * Time: 8:01 PM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\EmailAddress;

/**
 * CreateCompanyEmail.
 *
 * Creates an email address for a company.
 */
class CreateCompanyEmail extends QueryCommand
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
     * CreateCompanyEmail constructor.
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
        // begin transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            // create email address
            $this->tableGateway->setTable('EmailAddress');
            $matches = $this->tableGateway->findBy('emailAddress', $this->emailAddress->getEmailAddress());

            $emailId = null;
            if (count($matches) > 0) {
                // email exists
                $emailId = $matches[0]['id'];
            } else {
                // email does not exist
                $emailId = $this->tableGateway->create(array('emailAddress' => $this->emailAddress->getEmailAddress()));
            }

            // fetch email type id
            $this->tableGateway->setTable('L_EmailAddressType');
            $emailType = $this->tableGateway->findBy('description', $this->emailAddress->getType());

            // create relationship
            $this->tableGateway->setTable('R_CompanyEmailAddress');
            $this->tableGateway->create(array(
                'companyId' => $this->companyId,
                'emailAddressId' => $emailId,
                'typeId' => $emailType[0]['id']
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            return $this->emailAddress->setId($emailId);

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