<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/22/18
 * Time: 5:47 AM
 */

namespace PapaLocal\Data\Command\Person;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * PersonHasEmail.
 *
 * @package PapaLocal\Data\Command\Person
 */
class PersonHasEmail extends QueryCommand
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
     * PersonHasEmail constructor.
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
     *
     * @return bool        true if the person owns a matching email record
     */
    protected function runQuery()
    {
        // fetch the email id
        $this->tableGateway->setTable('EmailAddress');
        $emailRows = $this->tableGateway->findBy('emailAddress', $this->emailAddress->getEmailAddress());

        if (count($emailRows) < 1) {
            return false;
        }

        $this->tableGateway->setTable('R_PersonEmailAddress');
        $persEmailRows = $this->tableGateway->findByColumns(array(
            'personId' => $this->personId,
            'emailId' => $emailRows[0]['id']
        ));

        return (count($persEmailRows) >= 1);
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}