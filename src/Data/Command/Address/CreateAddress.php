<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/18/18
 * Time: 6:16 PM
 */

namespace PapaLocal\Data\Command\Address;

use PapaLocal\Entity\AddressInterface;
use PapaLocal\Data\Command\QueryCommand;

/**
 * CreateAddress.
 *
 * @package PapaLocal\Data\Command\Address
 */
class CreateAddress extends QueryCommand
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * CreateAddress constructor.
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

            // create address record
            $this->tableGateway->setTable('Address');

            $row = $this->serializer->normalize($this->address, 'array', array(
                'attributes' => array(
                    'streetAddress',
                    'city',
                    'state',
                    'postalCode',
                    'country'
                )
            ));

            return $this->tableGateway->create($row);

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