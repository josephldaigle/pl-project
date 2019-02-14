<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:55 AM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateDescriptionHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateDescriptionHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateDescriptionHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateDescription $command
     */
    function __invoke(UpdateDescription $command)
    {
        $this->tableGateway->setTable('ReferralAgreement');
        $record = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if (strcmp($command->getDescription(), $record['description']) !== 0) {
            $record['description'] = $command->getDescription();
            $this->tableGateway->update($record->properties());
        }

        return;
    }
}