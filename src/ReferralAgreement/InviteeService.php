<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/15/18
 * Time: 7:56 AM
 */


namespace PapaLocal\ReferralAgreement;


use PapaLocal\Core\Service\ServiceInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\Exception\RemoveInviteeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Registry;


/**
 * Class InviteeService
 *
 * @package PapaLocal\ReferralAgreement
 */
class InviteeService implements ServiceInterface
{
    /**
     * @var InviteeRepository
     */
    private $inviteeRepository;

    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $mysqlMsgFactory;

    /**
     * InviteeService constructor.
     *
     * @param InviteeRepository   $inviteeRepository
     * @param Registry            $workflowRegistry
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory      $mysqlMsgFactory
     */
    public function __construct(InviteeRepository $inviteeRepository, Registry $workflowRegistry, MessageBusInterface $mysqlBus, MessageFactory $mysqlMsgFactory)
    {
        $this->inviteeRepository = $inviteeRepository;
        $this->workflowRegistry  = $workflowRegistry;
        $this->mysqlBus          = $mysqlBus;
        $this->mysqlMsgFactory   = $mysqlMsgFactory;
    }

    /**
     * @param ReferralAgreementInvitee $referralAgreementInvitee
     *
     * @throws \Exception
     */
    public function saveInvitee(ReferralAgreementInvitee $referralAgreementInvitee)
    {
        // start workflow
        $workflow = $this->workflowRegistry->get($referralAgreementInvitee, 'agreement_invitee');
        $workflow->apply($referralAgreementInvitee, 'create');
    }

    /**
     * Rescind an invitation to an agreement.
     *
     * @param GuidInterface $inviteeGuid
     * @param GuidInterface $agreementGuid
     *
     * @throws RemoveInviteeException
     */
    public function removeInvitee(GuidInterface $inviteeGuid, GuidInterface $agreementGuid)
    {
        // load invitee
        $invitee = $this->inviteeRepository->findByGuid($inviteeGuid);

        $inviteeList = $this->inviteeRepository->findAllByAgreementGuid($agreementGuid);

        dump($inviteeList);
        if ($inviteeList->count() <= 1) {
            throw new RemoveInviteeException('The agreement must have at least 1 invitee present.');
        }

        $workflow = $this->workflowRegistry->get($invitee, 'agreement_invitee');
        $workflow->apply($invitee, 'remove');

        return;
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $inviteeUserGuid
     */
    public function acceptInvitation(GuidInterface $agreementGuid, GuidInterface $inviteeUserGuid)
    {
        // fetch invitee from storage
        $inviteeList = $this->inviteeRepository->findByCols([
            'agreementGuid' => $agreementGuid->value(),
            'userGuid' => $inviteeUserGuid->value()
        ]);

        // invoke workflow
        $workflow = $this->workflowRegistry->get($inviteeList->first(), 'agreement_invitee');
        $workflow->apply($inviteeList->first(), 'accept');

        return;
    }

    /**
     * @param GuidInterface $agreementGuid
     * @param GuidInterface $inviteeUserGuid
     */
    public function declineInvitation(GuidInterface $agreementGuid, GuidInterface $inviteeUserGuid)
    {
        // fetch invitee from storage
        $inviteeList = $this->inviteeRepository->findByCols([
            'agreementGuid' => $agreementGuid->value(),
            'userGuid' => $inviteeUserGuid->value()
        ]);

        // invoke workflow
        $workflow = $this->workflowRegistry->get($inviteeList->first(), 'agreement_invitee');
        $workflow->apply($inviteeList->first(), 'decline');

        return;
    }

    /**
     * @param GuidInterface $invitationGuid
     */
    public function markInvitationAsSent(GuidInterface $invitationGuid)
    {
        $markSentCmd = $this->mysqlMsgFactory->newMarkInvitationSent($invitationGuid);
        $this->mysqlBus->dispatch($markSentCmd);
        return;
    }
}