<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/9/18
 * Time: 7:51 PM
 */

namespace PapaLocal\Data\Command\Company;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;


/**
 * UpdateAddress.
 *
 * @package PapaLocal\Data\Command\Company
 */
class UpdateAddress extends QueryCommand
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
     * UpdateAddress constructor.
     *
     * @param int              $companyId
     * @param Address $address
     */
    public function __construct(int $companyId, Address$address)
    {
        $this->companyId = $companyId;
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
            $this->tableGateway->setTable('L_AddressType');
            $typeRows = $this->tableGateway->findBy('description', $this->address->getType());

            if (count($typeRows) !== 1) {
                throw new QueryCommandFailedException(sprintf('Unable to find address type: %s',
                    $this->address->getType()));
            }

            $this->tableGateway->setTable('Address');
            $addressRows = $this->tableGateway->findByColumns($this->serializer->normalize($this->address, 'array', array('attributes' => array(
                'streetAddress',
                'city',
                'state',
                'postalCode',
                'country'
            ))));

            if (count($addressRows) < 1) {
                $addressId = $this->tableGateway->create($this->serializer->normalize($this->address, 'array', array('attributes' => array(
                    'streetAddress',
                    'city',
                    'state',
                    'postalCode',
                    'country'
                ))));
            } else {
                $addressId = $addressRows[0]['id'];
            }

            $this->tableGateway->setTable('R_CompanyAddress');
            $compAddrRows = $this->tableGateway->findByColumns(array(
                'companyId' => $this->companyId,
                'typeId' => $typeRows[0]['id']
            ));

            if (count($compAddrRows) !== 1) {
                throw new QueryCommandFailedException(
                    sprintf('Unexpected row count returned for R_CompanyAddress: %s, company id: %s, count: %s',
                        $this->emailAddress->getType(), $this->companyId, count($compAddrRows)));
            }

            $compAddrRows[0]['addressId'] = $addressId;

            $result = $this->tableGateway->update($compAddrRows[0]);

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