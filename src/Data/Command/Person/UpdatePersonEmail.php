<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/21/18
 * Time: 1:46 PM
 */

namespace PapaLocal\Data\Command\Person;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * UpdatePersonEmail.
 *
 * @package PapaLocal\Data\Command\Person
 */
class UpdatePersonEmail extends QueryCommand
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
     * UpdatePersonEmail constructor.
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
        try {
            // load or create email address record
            $this->tableGateway->connection->beginTransaction();

            $this->tableGateway->setTable('EmailAddress');
            $emailRows = $this->tableGateway->findBy('emailAddress', $this->emailAddress->getEmailAddress());

            if (count($emailRows) < 1) {
                // the email address does not exist
                $emailId = $this->tableGateway->create(array('emailAddress' => $this->emailAddress->getEmailAddress()));
            } else {
                $emailId = $emailRows[0]['id'];
            }

            // fetch the email address type
            $this->tableGateway->setTable('L_EmailAddressType');
            $emailTypeRows = $this->tableGateway->findBy('description', $this->emailAddress->getType());

            if (count($emailTypeRows) < 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find email type: %s',
                    $this->emailAddress->getType()));
            }

            // modify or create the relationship
            $this->tableGateway->setTable('R_PersonEmailAddress');
            $persEmailRows = $this->tableGateway->findByColumns(array(
                'personId' => $this->personId,
                'typeId' => $emailTypeRows[0]['id']
            ));

            $affRows = 0;
            if (count($persEmailRows) < 1) {

                // relationship does not exist for type
                $affRows = $this->tableGateway->create(array(
                    'personId' => $this->personId,
                    'emailId' => $emailId,
                    'typeId' => $emailTypeRows[0]['id']
                ));

            } else {
                $persEmailRows[0]['emailId'] = $emailId;
                $affRows = $this->tableGateway->update($persEmailRows[0]);
            }

            // commit transaction
            $this->tableGateway->connection->commit();

            return $affRows;

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