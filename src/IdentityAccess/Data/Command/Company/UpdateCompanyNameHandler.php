<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 7:13 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateCompanyNameHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyNameHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateCompanyNameHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateCompanyName $command
     *
     * @throws CommandException
     */
    public function __invoke(UpdateCompanyName $command)
    {
        $this->tableGateway->setTable('Company');
        $companyRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

        if ($companyRecord->isEmpty()) {
            throw new CommandException(sprintf('Unable to find a company with guid: %s', $command->getCompanyGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $companyRecord['name'] = $command->getName();
        $this->tableGateway->update($companyRecord->properties());

        return;
    }


}