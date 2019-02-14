<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 2/6/19
 */


namespace PapaLocal\ReferralAgreement\Message\Command\Invitee;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Form\Invitee\RemoveInviteeForm;
use PapaLocal\ReferralAgreement\InviteeService;


/**
 * Class RemoveInviteeHandler.
 *
 * @package PapaLocal\ReferralAgreement\Message\Command\Invitee
 */
class RemoveInviteeHandler
{
    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * RemoveInviteeHandler constructor.
     *
     * @param InviteeService         $inviteeService
     * @param GuidGeneratorInterface $guidFactory
     */
    public function __construct(
        InviteeService $inviteeService,
        GuidGeneratorInterface $guidFactory
    )
    {
        $this->inviteeService    = $inviteeService;
        $this->guidFactory       = $guidFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RemoveInviteeForm $command)
    {
        $inviteeGuid = $this->guidFactory->createFromString($command->getInviteeGuid());
        $agreementGuid = $this->guidFactory->createFromString($command->getAgreementGuid());

        $this->inviteeService->removeInvitee($inviteeGuid, $agreementGuid);
    }

}