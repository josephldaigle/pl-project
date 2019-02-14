<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/30/17
 * Time: 8:36 PM
 */

namespace PapaLocal\Data\Command\Person;

use PapaLocal\Data\AttrType;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Data\Command\QueryCommand;

class CreatePersonEmail extends QueryCommand
{
    /**
     * @var int
     */
    private $personId;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * CreatePersonEmail constructor.
     *
     * @param int          $personId
     * @param EmailAddress $emailAddress
     */
    public function __construct(int $personId, EmailAddress $emailAddress)
    {
        $this->personId = $personId;
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

            // create relationship to person
            $this->tableGateway->setTable('R_PersonEmailAddress');
            $relationId = $this->tableGateway->create(array(
                'personId' => $this->personId,
                'emailId' => $emailId,
                'typeId' => intval($emailType[0]['id'])
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            $this->emailAddress->setId($emailId);
            return $this->emailAddress;
            
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