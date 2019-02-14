<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 9:37 PM
 */

namespace PapaLocal\Data\Command\Company;

use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * CreateCompanyAddress.
 *
 * Creates an Address, and assigns it to a Company.
 */
class CreateCompanyAddress extends QueryCommand
{
    /**
     * @var int
     */
    private $companyId;

    /**
     * @var Address
     */
    private $address;

    /**
     * CreateCompanyAddress constructor.
     *
     * @param int     $companyId
     * @param Address $address
     */
    public function __construct(int $companyId, Address $address)
    {
        $this->companyId = $companyId;
        $this->address   = $address;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        // start transaction
        $this->tableGateway->connection->beginTransaction();

        try {

            // create the address
            $row = $this->serializer->normalize($this->address, 'array');
            unset($row['type']);

            $this->tableGateway->setTable('Address');
            $addressRows = $this->tableGateway->findByColumns(array(
                'streetAddress' => $this->address->getStreetAddress(),
                'city'          => $this->address->getCity(),
                'state'         => $this->address->getState(),
                'postalCode'    => $this->address->getPostalCode(),
                'country'       => $this->address->getCountry(),
            ));

            if (count($addressRows) < 1) {
                $addressId = $this->tableGateway->create($row);
            } else {
                $addressId = $addressRows[0]['id'];
            }

            // create the relationship
            $this->tableGateway->setTable('L_AddressType');
            $typeRows = $this->tableGateway->findBy('description', $this->address->getType());

            if (count($typeRows) !== 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find address type %s',
                    $this->address->getType()));
            }

            $this->tableGateway->setTable('R_CompanyAddress');
            $id = $this->tableGateway->create(array(
                'companyId' => $this->companyId,
                'addressId' => $addressId,
                'typeId'    => $typeRows[0]['id'],
            ));

            // commit transaction
            $this->tableGateway->connection->commit();

            return $this->address;

        } catch (\Exception $e) {

            // rollback transaction
            $this->tableGateway->connection->rollBack();

            throw $this->filterException($e);
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