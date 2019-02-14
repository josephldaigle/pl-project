<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 5:34 PM
 */

namespace PapaLocal\ReferralAgreement\Workflow\Invitee;


use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Notification\NotificationFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredAcceptedSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Workflow\Invitee
 */
class EnteredAcceptedSubscriber implements EventSubscriberInterface
{
    /**
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EnteredAcceptedSubscriber constructor.
     *
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param Notifier                    $notifier
     * @param NotificationFactory         $notificationFactory
     * @param LoggerInterface             $logger
     */
    public function __construct(
        ReferralAgreementRepository $referralAgreementRepository,
        Notifier $notifier,
        NotificationFactory $notificationFactory,
        LoggerInterface $logger
    )
    {
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->notifier                    = $notifier;
        $this->notificationFactory         = $notificationFactory;
        $this->logger                      = $logger;
    }

    /**
     * @param Event $event
     *
     * @throws \PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException
     */
    public function enteredAccepted(Event $event)
    {
        // fetch invitee
        $invitee = $event->getSubject();

        // fetch agreement
        $agreement = $this->referralAgreementRepository->findByGuid($invitee->getGuid());

        try {
            // send notification to owner
            $notification = $this->notificationFactory->newInvitationAccepted($invitee->getFirstName(), $invitee->getLastName);

            $this->notifier->sendUserNotification($agreement->getOwnerGuid(), $notification);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Unable to send notification to user [%s] due to exception at line %s of file %s: %s', $agreement->getOwnerGuid(), $exception->getLine(), $exception->getFile(), $exception->getMessage()), array($exception));
        }

        return;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.agreement_invitee.entered.Active' => 'enteredAccepted'
        ];
    }

}