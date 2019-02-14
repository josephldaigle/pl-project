<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/24/18
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateCompanyAddressHandler.
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyAddressHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateCompanyAddressHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateCompanyAddress $command
     *
     * @throws CommandException
     */
    public function __invoke(UpdateCompanyAddress $command)
    {
        $this->tableGateway->setTable('v_company_address');
        $addressRecords = $this->tableGateway->findByColumns([
            'companyGuid' => $command->getCompanyGuid(),
            'streetAddress' => $command->getStreetAddress(),
            'city' => $command->getCity(),
            'state' => $command->getState(),
            'postalCode' => $command->getPostalCode()
        ]);

        if (! $addressRecords->count() > 0) {
            // fetch address id
            $this->tableGateway->setTable('Company');
            $companyRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

            $this->tableGateway->setTable('L_AddressType');
            $addressTypeRecs = $this->tableGateway->findBy('description', $command->getType());

            $this->tableGateway->setTable('Address');
            $addressRecords = $this->tableGateway->findByColumns([
                'streetAddress' => $command->getStreetAddress(),
                'city' => $command->getCity(),
                'state' => $command->getState(),
                'postalCode' => $command->getPostalCode()
            ]);

            $addressId = null;
            if (! $addressRecords->count() > 0) {

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
                    // address was not created
                    throw new CommandException(sprintf('Unable to save address for company %s.', $command->getCompanyGuid()));
                }

                $addressId = $newAddressRecs->current()['id'];
            } else {
                $addressId = $addressRecords->current()['id'];
            }

            $this->tableGateway->setTable('R_CompanyAddress');
            $coAddrRecs = $this->tableGateway->findByColumns([
                'companyId' => $companyRecord['id'],
                'typeId' => $addressTypeRecs->current()['id']
            ]);

            if ($coAddrRecs->count() > 0) {
                $this->tableGateway->delete($coAddrRecs->current()['id']);
            }

            $this->tableGateway->create([
                'companyId' => $companyRecord['id'],
                'addressId' => $addressId,
                'typeId' => $addressTypeRecs->current()['id']
            ]);
        }

        return;
    }
}