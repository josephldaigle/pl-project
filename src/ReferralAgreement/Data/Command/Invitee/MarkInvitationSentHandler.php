<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/11/18
 * Time: 4:17 PM
 */

namespace PapaLocal\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class MarkInvitationSentHandler
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Invitee
 */
class MarkInvitationSentHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * MarkInvitationSentHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param MarkInvitationSent $command
     */
    function __invoke(MarkInvitationSent $command)
    {
        $this->tableGateway->setTable('ReferralAgreementInvitee');
        $inviteeRecord = $this->tableGateway->findByGuid($command->getInvitationGuid());

        if (! $inviteeRecord->isEmpty()) {
            $inviteeRecord['timeSent'] = date("Y-m-d H:i:s", time());
            $this->tableGateway->update($inviteeRecord->properties());
        }

        return;
    }
}