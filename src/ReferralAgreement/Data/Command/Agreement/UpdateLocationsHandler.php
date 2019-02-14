<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:12 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class UpdateAgreementLocationsHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class UpdateLocationsHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * UpdateAgreementLocationsHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateLocations $command
     *
     * @throws CommandException
     */
    function __invoke(UpdateLocations $command)
    {
        // fetch agreement table row id
        $this->tableGateway->setTable('v_referral_agreement');

        $agmtRecord = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($agmtRecord->isEmpty()) {
            throw new CommandException(sprintf('Unable to locate a referral agreement with guid %s.', $command->getAgreementGuid()), CommandExceptionCode::NOT_FOUND());
        }

        // fetch existing locations
        $this->tableGateway->setTable('ReferralAgreementLocation');

        // remove existing locations
        $existingLocs = $this->tableGateway->findBy('agreementId', $agmtRecord['id']);

        foreach ($existingLocs as $location) {
            $this->tableGateway->delete($location['id']);
        }

        // save new locations
        foreach ($command->getLocations() as $location) {
            $this->tableGateway->create(array(
                'agreementId' => $agmtRecord['id'],
                'location' => $location['location'],
                'type' => $location['type']
            ));
        }
    }
}