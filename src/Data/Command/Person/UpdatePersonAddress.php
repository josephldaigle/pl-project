<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/1/18
 * Time: 6:31 AM
 */

namespace PapaLocal\Data\Command\Person;

use PapaLocal\Data\Command\Address\CreateAddress;
use PapaLocal\Data\Command\Address\LoadAddress;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\AddressInterface;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * UpdatePersonAddress.
 *
 * Update a person-to-address relationship, using an existing address.
 *
 * @package Test\Functional\Data\Command\Person
 */
class UpdatePersonAddress extends QueryCommand
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
     * UpdatePersonAddress constructor.
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
        // start transaction
        $this->tableGateway->connection->beginTransaction();

        try {

            // load address row
            $this->tableGateway->setTable('Address');
            $loadAddrCmd = $this->commandFactory->createCommand(LoadAddress::class, array($this->address));
            $address = $loadAddrCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            if (is_null($address->getId())) {
                // address does not exist, create it
                $createAddrCmd = $this->commandFactory->createCommand(CreateAddress::class, array($this->address));
                $address->setId($createAddrCmd->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory));
            }

            // fetch address type id
            $this->tableGateway->setTable('L_AddressType');
            $typeId = $this->tableGateway->findBy('description', $this->address->getType());

            if (count($typeId) < 1) {
                // type not found
                throw new QueryCommandFailedException(sprintf('Unable to find address type for %s',
                    $this->address->getType()));
            }

            // save relationship
            $this->tableGateway->setTable('R_PersonAddress');
            $persAddrRows = $this->tableGateway->findByColumns(array(
                'personId' => $this->personId,
                'typeId' => $typeId[0]['id']
            ));

            if (count($persAddrRows) !== 1) {
                throw new QueryCommandFailedException(
                    sprintf('Found unexpected number of address records for person: [%s]: %s',
                    $this->personId, count($persAddrRows)));
            }

            $persAddrRows[0]['addressId'] = $address->getId();
            $result = $this->tableGateway->update($persAddrRows[0]);

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