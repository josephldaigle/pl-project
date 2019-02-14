<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/2/19
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateReferralPriceHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateReferralPriceHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateReferralPriceHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateReferralPrice $command)
    {
        $this->tableGateway->setTable('ReferralAgreement');
        $record = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($record->isEmpty()) {
            throw new CommandException(sprintf("Unable to locate an agreement with guid: %s.", $command->getAgreementGuid()), CommandExceptionCode::NOT_FOUND());
        }

        $record['bid'] = $command->getPrice();

        $this->tableGateway->update($record->properties());
        return;
    }


}