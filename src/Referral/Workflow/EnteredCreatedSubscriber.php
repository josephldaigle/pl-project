<?php
/**
 * Created by PhpStorm.
 * Date: 9/12/18
 * Time: 2:09 PM
 */

namespace PapaLocal\Referral\Workflow;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Data\Ewebify;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\Notification\Notifier;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredCreatedSubscriber
 * @package PapaLocal\Referral\Workflow
 */
class EnteredCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailMessageBuilder
     */
    private $emailMessageBuilder;

    /**
     * @var Emailer
     */
    private $emailer;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     *
     * Identity Access
     */
    private $iaMessageFactory;

    /**
     * EnteredCreatedSubscriber constructor.
     * @param EmailMessageBuilder $emailMessageBuilder
     * @param Emailer $emailer
     * @param NotificationFactory $notificationFactory
     * @param Notifier $notifier
     * @param MessageBusInterface $appBus
     * @param MessageFactory $iaMessageFactory
     */
    public function __construct(EmailMessageBuilder $emailMessageBuilder, Emailer $emailer, NotificationFactory $notificationFactory, Notifier $notifier, MessageBusInterface $appBus, MessageFactory $iaMessageFactory)
    {
        $this->emailMessageBuilder = $emailMessageBuilder;
        $this->emailer = $emailer;
        $this->notificationFactory = $notificationFactory;
        $this->notifier = $notifier;
        $this->appBus = $appBus;
        $this->iaMessageFactory = $iaMessageFactory;
    }


    /**
     * @param Event $event
     */
    public function createReferral(Event $event)
    {
        if ($event->getSubject()->getRecipient() instanceof ContactRecipient) {

            $recipient = $event->getSubject()->getRecipient();

            $query = $this->iaMessageFactory->newFindUserByGuid($event->getSubject()->getProviderUserGuid());
            $provider = $this->appBus->dispatch($query);

            // Send email only to contact
            $email = $this->emailMessageBuilder
                ->subject(sprintf('Your Business Has Received A New Referral From %s %s.', $provider->getFirstName(), $provider->getLastName()))
                ->from(Ewebify::ADMIN_EMAIL)
                ->sendTo($recipient->getEmailAddress()->getEmailAddress())
                ->usingTwigTemplate('emails/referral/referralInvitation.html.twig', array('recipient' => $recipient, 'provider' => $provider))
                ->build();
            $this->emailer->send($email);

            $providerNotification = $this->notificationFactory->newReferralInvitationConfirmation($recipient, $provider);
            $this->notifier->sendUserNotification($event->getSubject()->getProviderUserGuid(), $providerNotification);
        }
        return;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.referral_delivering.entered.created' => array('createReferral')
        ];
    }
}