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
 * Class UpdateCompanyEmailHandler.
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyEmailHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateCompanyEmailHandler constructor.
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
    public function __invoke(UpdateCompanyEmail $command)
    {
        $this->tableGateway->setTable('v_company_email_address');
        $emailRecords = $this->tableGateway->findByColumns([
            'companyGuid'  => $command->getCompanyGuid(),
            'emailAddress' => $command->getEmailAddress(),
        ]);

        if ( ! $emailRecords->count() > 0) {
            $this->tableGateway->setTable('Company');
            $companyRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

            $this->tableGateway->setTable('L_EmailAddressType');
            $emailTypeRecords = $this->tableGateway->findBy('description', $command->getEmailAddressType());

            $this->tableGateway->setTable('EmailAddress');
            $emailRecords = $this->tableGateway->findBy('emailAddress', $command->getEmailAddress());

            $emailId = null;
            if ($emailRecords->count() > 0) {
                $emailId = $emailRecords->current()['id'];
            } else {
                $this->tableGateway->create(['emailAddress' => $command->getEmailAddress()]);

                $newEmailRecords = $this->tableGateway->findBy('emailAddress', $command->getEmailAddress());
                if ( ! $newEmailRecords->count() > 0) {
                    throw new CommandException(sprintf('Unable to save email address %s for company %s.',
                        $command->getEmailAddress(), $command->getCompanyGuid()));
                }

                $emailId = $newEmailRecords->current()['id'];
            }

            $this->tableGateway->setTable('R_CompanyEmailAddress');
            $coEmailRecs = $this->tableGateway->findByColumns([
                'companyId' => $companyRecord['id'],
                'typeId' => $emailTypeRecords->current()['id']
            ]);

            if ($coEmailRecs->count() > 0) {
                $this->tableGateway->delete($coEmailRecs->current()['id']);
            }

            $this->tableGateway->create([
                'companyId'      => $companyRecord['id'],
                'emailAddressId' => $emailId,
                'typeId'         => $emailTypeRecords->current()['id'],
            ]);

        }

        return;
    }

}