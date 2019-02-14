<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/5/18
 * Time: 8:04 PM
 */


namespace PapaLocal\ReferralAgreement\Workflow\Agreement;


use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Notification\NotificationFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;


/**
 * Class EnteredActiveSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Agreement
 */
class EnteredActiveSubscriber implements EventSubscriberInterface
{
    /**
     * @var Registry
     */
    private $workflowRegistry;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var  NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EnteredActiveSubscriber constructor.
     *
     * @param Registry            $workflowRegistry
     * @param Notifier            $notifier
     * @param NotificationFactory $notificationFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(
        Registry $workflowRegistry,
        Notifier $notifier,
        NotificationFactory $notificationFactory,
        LoggerInterface $logger
    )
    {
        $this->workflowRegistry    = $workflowRegistry;
        $this->notifier            = $notifier;
        $this->notificationFactory = $notificationFactory;
        $this->logger              = $logger;
    }

    /**
     * @param Event $event
     */
    public function enteredActive(Event $event)
    {
        // send out invitations
        $agreement = $event->getSubject();
        $invitees = $agreement->getInvitees();

        // anytime an agreement is activated, new invitees should be invited and participants should be notified
        // this invokes the invitee workflow to update the state of an agreement
        foreach ($invitees as $invitee) {

            try {
                if ($invitee->getCurrentPlace() === 'Created') {
                    // invitee has not received initial invitation
                    $workflow = $this->workflowRegistry->get($invitee, 'agreement_invitee');
                    $workflow->apply($invitee, 'invite');

                } elseif ($invitee->isParticipant()) {
                    // notify participant of activation via app only
                    $notification = $this->notificationFactory->newAgreementStatusChanged($invitee->getEmailAddress()->getEmailAddress(), $agreement->getName(), $agreement->getStatusHistory()->getCurrentStatus());

                    $this->notifier->sendUserNotification($invitee->getUserId(), $notification);
                }

            } catch (\Exception $exception) {
                $this->logger->warning(sprintf('Could not send agreement invitation to %s.', $invitee->getEmailAddress()->getEmailAddress()), array($exception));
            }

        }

        return;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_agreement.entered.Active' => 'enteredActive'
        ];
    }

}