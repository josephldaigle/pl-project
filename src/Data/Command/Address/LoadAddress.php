<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/26/18
 * Time: 8:29 PM
 */

namespace PapaLocal\Data\Command\Address;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\AddressInterface;

/**
 * LoadAddress.
 *
 * Loads an address from storage. Performs a search using the members present on
 * constructor arg $address.
 */
class LoadAddress extends QueryCommand
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * LoadAddress constructor.
     *
     * @param AddressInterface $address
     */
    public function __construct(AddressInterface $address)
    {
        $this->address = $address;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            $this->tableGateway->setTable('Address');

            if (! is_null($this->address->getId())) {
                // address has id
                $rows = $this->tableGateway->findById($this->address->getId());
            } else {
                $addressArr = $this->serializer->normalize($this->address, 'array', array(
                    'attributes' => array(
                        'streetAddress',
                        'city',
                        'state',
                        'postalCode',
                        'country'
                    )
                ));
                $rows = $this->tableGateway->findByColumns($addressArr);
            }

            if (count($rows) > 0) {
                $address = $this->serializer->denormalize($rows[0], Address::class, 'array');
            } else {
                $address = $this->serializer->denormalize(array(), Address::class, 'array');
            }

            return $address;

        } catch (\Exception $exception) {

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