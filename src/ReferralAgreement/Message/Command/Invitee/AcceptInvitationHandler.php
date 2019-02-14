<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/14/18
 * Time: 9:16 PM
 */

namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\ReferralAgreement\InviteeService;
use PapaLocal\ReferralAgreement\ReferralAgreementService;


/**
 * Class AcceptInvitationHandler
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class AcceptInvitationHandler
{
    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * AcceptInvitationHandler constructor.
     *
     * @param InviteeService $inviteeService
     */
    public function __construct(InviteeService $inviteeService)
    {
        $this->inviteeService = $inviteeService;
    }

    /**
     * @param AcceptInvitation $command
     */
    function __invoke(AcceptInvitation $command)
    {
        $this->inviteeService->acceptInvitation($command->getAgreementGuid(), $command->getUserGuid());
        return;
    }


}