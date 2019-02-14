<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/21/18
 * Time: 9:12 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateCompanyPhoneHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyPhoneHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateCompanyPhoneHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     * @throws CommandException
     */
    public function __invoke(UpdateCompanyPhone $command)
    {
        // query phone number for company
        $this->tableGateway->setTable('v_company_phone_number');
        $phoneRecords = $this->tableGateway->findByColumns([
            'companyGuid' => $command->getCompanyGuid(),
            'phoneNumber' => $command->getPhoneNumber()]
        );

        if (! $phoneRecords->count() > 0) {
            // fetch company id
            $this->tableGateway->setTable('Company');
            $companyRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

            $this->tableGateway->setTable('L_PhoneNumberType');
            $phoneTypeRecords = $this->tableGateway->findBy('description', $command->getPhoneNumberType());

            // the phone number is not already assigned to the company
            // see if the phone number already exists in db
            $this->tableGateway->setTable('PhoneNumber');
            $phoneRecords = $this->tableGateway->findBy('phoneNumber', $command->getPhoneNumber());

            $phoneId = null;
            if ($phoneRecords->count() > 0) {
                // phone number exists in db
                $phoneId = $phoneRecords->current()['id'];
            } else {
                // phone number does not exist in db
                $this->tableGateway->create(['phoneNumber' => $command->getPhoneNumber()]);

                $newPhoneRecs = $this->tableGateway->findBy('phoneNumber', $command->getPhoneNumber());
                if (! $newPhoneRecs->count() > 0 ) {
                    // phone number was not created
                    throw new CommandException(sprintf('Unable to save phone number %s for company %s.',$command->getPhoneNumber(), $command->getCompanyGuid()));
                }

                $phoneId = $newPhoneRecs->current()['id'];
            }

            $this->tableGateway->setTable('R_CompanyPhoneNumber');
            $coPhoneRecs = $this->tableGateway->findByColumns([
                'companyId' => $companyRecord['id'],
                'typeId' => $phoneTypeRecords->current()['id']
            ]);

            if ($coPhoneRecs->count() > 0) {
                $this->tableGateway->delete($coPhoneRecs->current()['id']);
            }
            $this->tableGateway->create([
                'companyId' => $companyRecord['id'],
                'phoneId' => $phoneId,
                'typeId' => $phoneTypeRecords->current()['id']
            ]);

            return;
        }

        // phone number already exists, do nothing
        return;
    }
}