<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/3/18
 * Time: 7:21 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class SaveInviteeHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class SaveInviteeHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * SaveAgreementInviteeHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param SaveInvitee $command
     *
     * @throws CommandException
     */
    function __invoke(SaveInvitee $command)
    {
        $this->tableGateway->setTable('v_referral_agreement');
        $agmtRecord = $this->tableGateway->findByGuid($command->getAgreementGuid());

        if ($agmtRecord->isEmpty()) {
            throw new CommandException(sprintf('Unable to find an agreement with guid: %s in %s', $command->getAgreementGuid(), __METHOD__), CommandExceptionCode::NOT_FOUND());
        }

        try {

            $this->tableGateway->setTable('ReferralAgreementInvitee');

            $this->tableGateway->create(array(
                'guid' => $command->getInviteeGuid(),
                'agreementId' => $agmtRecord['id'],
                'firstName' => $command->getFirstName(),
                'lastName' => $command->getLastName(),
                'message' => $command->getMessage(),
                'emailAddress' => $command->getEmailAddress(),
                'phoneNumber' => $command->getPhoneNumber(),
                'userGuid' => $command->getUserGuid(),
            ));

            return;

        } catch (\Exception $exception) {
            throw new CommandException('Unable to save the referral agreement invitee.', CommandExceptionCode::UNSPECIFIED(), $exception);
        }
    }

}