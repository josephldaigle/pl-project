<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/19/18
 * Time: 9:48 PM
 */

namespace PapaLocal\Data\Command\Address;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\AddressInterface;

/**
 * AddressExists.
 *
 * Check whether or not an address exists in storage.
 *
 * @package PapaLocal\Data\Command\Address
 */
class AddressExists extends QueryCommand
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * AddressExists constructor.
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

            return (count($rows) > 0) ? $rows[0]['id'] : false;

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