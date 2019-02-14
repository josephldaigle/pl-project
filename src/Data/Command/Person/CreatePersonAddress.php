<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/23/17
 * Time: 12:12 AM
 */

namespace PapaLocal\Data\Command\Person;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\AddressInterface;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * CreatePersonAddress.
 *
 * Creates an address for a person.
 */
class CreatePersonAddress extends QueryCommand
{
    /**
     * @var int
     */
    private $personId;

    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * CreatePersonAddress constructor.
     *
     * @param int              $personId
     * @param AddressInterface $address
     */
    public function __construct(int $personId, AddressInterface $address)
    {
        $this->personId = $personId;
        $this->address = $address;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // begin transaction
        $this->tableGateway->connection->beginTransaction();

        try {
            // create address
            $this->tableGateway->setTable('Address');
            $row = $this->serializer->normalize($this->address, 'array', array('attributes' => array(
                'streetAddress', 'city', 'state', 'postalCode', 'country')));
            $addressId = $this->tableGateway->create($row);

            // fetch address type id
            $this->tableGateway->setTable('L_AddressType');
            $typeId = $this->tableGateway->findBy('description', $this->address->getType());

            if (count($typeId) < 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find type for %s',
                    $this->address->getType()));
            }

            // save relationship
            $this->tableGateway->setTable('R_PersonAddress');
            $persAddrId = $this->tableGateway->create(array(
                'personId' => $this->personId,
                'addressId' => $addressId,
                'typeId' => $typeId[0]['id']
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            return $persAddrId;

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