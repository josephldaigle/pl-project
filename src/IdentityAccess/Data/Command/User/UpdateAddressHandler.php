<?php
/**
 * Created by PhpStorm.
 * User: Joe
 * Date: 12/29/18
 * Time: 9:15 AM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateAddressHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateAddressHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateAddressHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateAddress $command
     *
     * @throws CommandException
     */
    public function __invoke(UpdateAddress $command)
    {
        $this->tableGateway->setTable('v_user_address');
        $userAddrRecs = $this->tableGateway->findByColumns([
            'userGuid' => $command->getUserGuid(),
            'streetAddress' => $command->getStreetAddress(),
            'city' => $command->getCity(),
            'state' => $command->getState(),
            'postalCode' => $command->getPostalCode()
        ]);

        if (! $userAddrRecs->count() > 0) {
            $this->tableGateway->setTable('v_user');
            $userRecords =  $this->tableGateway->findBy('userGuid', $command->getUserGuid());

            $this->tableGateway->setTable('L_AddressType');
            $addrTypeRecs = $this->tableGateway->findBy('description', $command->getType());

            $this->tableGateway->setTable('Address');
            $addrRecs = $this->tableGateway->findByColumns([
                'streetAddress' => $command->getStreetAddress(),
                'city' => $command->getCity(),
                'state' => $command->getState(),
                'postalCode' => $command->getPostalCode()
            ]);

            $addressId = null;
            if (! $addrRecs->count() > 0) {
                $this->tableGateway->create([
                    'streetAddress' => $command->getStreetAddress(),
                    'city' => $command->getCity(),
                    'state' => $command->getState(),
                    'postalCode' => $command->getPostalCode(),
                    'country' => $command->getCountry()
                ]);

                $newAddressRecs = $this->tableGateway->findByColumns([
                    'streetAddress' => $command->getStreetAddress(),
                    'city' => $command->getCity(),
                    'state' => $command->getState(),
                    'postalCode' => $command->getPostalCode()
                ]);

                if (! $newAddressRecs->count() > 0) {
                    throw new CommandException(sprintf('Unable to save address for user %s.', $command->getUserGuid()));
                }

                $addressId = $newAddressRecs->current()['id'];
            } else {
                $addressId = $addrRecs->current()['id'];
            }

            $this->tableGateway->setTable('R_PersonAddress');
            $existingAddrRecs = $this->tableGateway->findByColumns([
                'personId' => $userRecords->current()['personId'],
                'typeId' => $addrTypeRecs->current()['id']
            ]);

            if ($existingAddrRecs->count() > 0) {
                $this->tableGateway->delete($existingAddrRecs->current()['id']);
            }

            $this->tableGateway->create([
                'personId' => $userRecords->current()['personId'],
                'addressId' => $addressId,
                'typeId' => $addrTypeRecs->current()['id']
            ]);
        }

        return;
    }

}