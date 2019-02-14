<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/24/18
 * Time: 7:44 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateAgreementNameHandler.
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementNameHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateAgreementNameHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }


    /**
     * @param UpdateAgreementName $command
     */
    public function __invoke(UpdateAgreementName $command)
    {
        $this->tableGateway->setTable('ReferralAgreement');
        $record = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if (strcmp($record['name'], $command->getNewName()) !== 0) {

            // only save if name is different from existing
            $record['name'] = $command->getNewName();
            $this->tableGateway->update($record->properties());
        }

        return;
    }
}