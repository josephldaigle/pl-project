<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 3:56 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Exception\AgreementExistsException;


/**
 * Class SaveAgreementHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class SaveAgreementHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * SaveAgreementHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param SaveAgreement $command
     *
     * @throws AgreementExistsException
     * @throws \Exception
     */
    function __invoke(SaveAgreement $command)
    {
        try {
            // insert row into agreement table
            $this->tableGateway->setTable('ReferralAgreement');
            
            $this->tableGateway->create(array(
                'guid' => $command->getGuid(),
                'name' => $command->getName(),
                'description' => $command->getDescription(),
                'strategy' => $command->getStrategy(),
                'quantity' => $command->getQuantity(),
                'bid' => $command->getBid(),
                'companyGuid' => $command->getCompanyGuid(),
                'ownerGuid' => $command->getOwnerGuid()
            ));

            return;

        } catch (\Exception $exception) {

            if (preg_match('/(Integrity constraint violation)(.)+(Duplicate entry)(.)+(UNIQUE_AGREEMENT)/', $exception->getMessage())) {
                throw new AgreementExistsException('An agreement already exists with that name.');
            }

            throw new CommandException($exception->getMessage(), CommandExceptionCode::UNSPECIFIED(), $exception);

        }
    }

}