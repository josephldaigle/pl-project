<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 9:22 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class CreateAgreementServiceHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command
 */
class UpdateServicesHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * CreateAgreementServiceHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param UpdateServices $command
     *
     * @throws CommandException
     */
    function __invoke(UpdateServices $command)
    {
        // fetch agreement table row id
        $this->tableGateway->setTable('v_referral_agreement');
        $agmtRecord = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($agmtRecord->isEmpty()) {
            throw new CommandException(sprintf('Unable to locate agreement with guid %s.', $command->getAgreementGuid()), CommandExceptionCode::NOT_FOUND());
        }

        // remove existing services
        $this->tableGateway->setTable('ReferralAgreementService');

        $existingSvcs = $this->tableGateway->findBy('agreementId', $agmtRecord['id']);

        foreach ($existingSvcs as $service) {
            $this->tableGateway->delete($service['id']);
        }

        // save new services
        foreach ($command->getServices() as $service) {
            $this->tableGateway->create(array(
                'agreementId' => $agmtRecord['id'],
                'service' => $service['service'],
                'type' => $service['type']
            ));
        }
    }

}