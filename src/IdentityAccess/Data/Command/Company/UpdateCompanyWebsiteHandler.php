<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:24 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateCompanyWebsiteHandler
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyWebsiteHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateCompanyWebsiteHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateCompanyWebsite $command
     *
     * @throws CommandException
     */
    public function __invoke(UpdateCompanyWebsite $command)
    {
        $this->tableGateway->setTable('Company');
        $coRecord = $this->tableGateway->findByGuid($command->getCompanyGuid());

        if ($coRecord->isEmpty()) {
            throw new CommandException(sprintf('Unable to locate a company with guid: %s.', $command->getCompanyGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $coRecord['website'] = $command->getWebsite();

        $this->tableGateway->update($coRecord->properties());

        return;
    }

}