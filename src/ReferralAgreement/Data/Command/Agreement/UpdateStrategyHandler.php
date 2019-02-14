<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/1/19
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateStrategyHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateStrategyHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateStrategyHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     *
     * @param UpdateStrategy $command
     *
     * @throws CommandException
     */
    public function __invoke(UpdateStrategy $command)
    {
        $this->tableGateway->setTable('ReferralAgreement');
        $record = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($record->isEmpty()) {
            throw new CommandException(sprintf('Unable to locate an agreement with guid %s.', $command->getAgreementGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $record['strategy'] = strtolower($command->getStrategy());

        $this->tableGateway->update($record->properties());
        return;
    }


}