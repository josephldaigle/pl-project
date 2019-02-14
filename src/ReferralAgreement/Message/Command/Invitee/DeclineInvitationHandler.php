<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/15/18
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\ReferralAgreement\InviteeService;


/**
 * Class DeclineInvitationHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class DeclineInvitationHandler
{
    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * DeclineInvitationHandler constructor.
     *
     * @param InviteeService $inviteeService
     */
    public function __construct(InviteeService $inviteeService)
    {
        $this->inviteeService = $inviteeService;
    }

    /**
     * @param DeclineInvitation $command
     */
    public function __invoke(DeclineInvitation $command)
    {
        $this->inviteeService->declineInvitation($command->getAgreementGuid(), $command->getUserGuid());
        return;
    }
}