<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/11/18
 * Time: 12:02 PM
 */

namespace PapaLocal\Data\Command\Person;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\PhoneNumber;

/**
 * CreatePersonPhone.
 *
 * @package PapaLocal\Data\Command\Person
 */
class CreatePersonPhone extends QueryCommand
{
    /**
     * @var int
     */
    private $personId;

    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * CreatePersonPhone constructor.
     *
     * @param int         $personId
     * @param PhoneNumber $phoneNumber
     */
    public function __construct(int $personId, PhoneNumber $phoneNumber)
    {
        $this->personId = $personId;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // begin transaction
        $this->tableGateway->connection->beginTransaction();

        try {

            $this->tableGateway->setTable('PhoneNumber');
            $phoneRows = $this->tableGateway->findBy('phoneNumber', $this->phoneNumber->getPhoneNumber());

            if (count($phoneRows) < 1) {
                $phoneId = $this->tableGateway->create(array('phoneNumber' => $this->phoneNumber->getPhoneNumber()));
            } else {
                $phoneId = $phoneRows[0]['id'];
            }

            // fetch phone type id
            $this->tableGateway->setTable('L_PhoneNumberType');
            $phoneType = $this->tableGateway->findBy('description', $this->phoneNumber->getType());

            // create relationship
            $this->tableGateway->setTable('R_PersonPhoneNumber');
            $id = $this->tableGateway->create(array(
                'personId' => $this->personId,
                'phoneId' => $phoneId,
                'typeId' => $phoneType[0]['id']
            ));

            $this->phoneNumber->setId($id);

            // commit transaction
            $this->tableGateway->connection->commit();

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