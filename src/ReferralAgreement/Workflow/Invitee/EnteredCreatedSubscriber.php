<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/4/18
 * Time: 9:35 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;


/**
 * Class EnteredCreatedSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class EnteredCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * EnteredCreatedSubscriber constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param Registry                    $workflowRegistry
     */
    public function __construct(
        ReferralAgreementRepository $referralAgreementRepository,
        Registry $workflowRegistry
    )
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->workflowRegistry            = $workflowRegistry;
    }

    /**
     * @param Event $event
     *
     * @throws \PapaLocal\Core\Exception\InvalidStateException
     * @throws \PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException
     */
    public function inviteeSaved(Event $event)
    {
        $invitee = $event->getSubject();

        // if agreement is active, send invitation to participate
        $agreement = $this->referralAgreementRepository->findByGuid($invitee->getAgreementId());
        if ($agreement->getCurrentPlace() === Status::ACTIVE()->getValue()) {
            $workflow = $this->workflowRegistry->get($invitee, 'agreement_invitee');
            $workflow->apply($invitee, 'invite');
        }

        return;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.entered.Created' => 'inviteeSaved'
        ];
    }

}