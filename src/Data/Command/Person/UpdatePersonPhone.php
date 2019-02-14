<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/26/18
 * Time: 8:32 PM
 */

namespace PapaLocal\Data\Command\Person;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;
use PapaLocal\Entity\PhoneNumber;

/**
 * UpdatePersonPhone.
 *
 * @package PapaLocal\Data\Command\Person
 */
class UpdatePersonPhone extends QueryCommand
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
     * UpdatePersonPhone constructor.
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
        // start transaction
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
            $phoneTypeRows = $this->tableGateway->findBy('description', $this->phoneNumber->getType());

            if (count($phoneTypeRows) !== 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find phone type: %s',
                    $this->phoneNumber->getType()));
            }

            $this->tableGateway->setTable('R_PersonPhoneNumber');

            // should only be one phone record per type
            $persPhoneRows = $this->tableGateway->findByColumns(array(
                'personId' => $this->personId,
                'typeId' => $phoneTypeRows[0]['id']
            ));

            if (count($persPhoneRows) !== 1) {
                throw new QueryCommandFailedException(
                    sprintf('Found unexpected number of phone records for person [%s]: %s',
                        $this->personId, count($persPhoneRows)));
            }

            $persPhoneRows[0]['phoneId'] = $phoneId;
            $result = $this->tableGateway->update($persPhoneRows[0]);

            // commit transaction
            $this->tableGateway->connection->commit();

            return $result;

        } catch (\Exception $exception) {
            // roll back transaction
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