<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 2:55 PM
 */

namespace PapaLocal\IdentityAccess\Workflow\User;


use PapaLocal\Notification\Account\RegisterUser;
use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\IdentityAccess\Event\EventFactory;
use PapaLocal\IdentityAccess\Event\UserRegistered;
use PapaLocal\Notification\Notifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredActiveSubscriber
 *
 * @package PapaLocal\IdentityAccess\Workflow\User
 */
class EnteredActiveSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EventFactory
     */
    private $iaEventFactory;

    /**
     * @var VOFactory
     */
    private $voFactory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EnteredActiveSubscriber constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param EventFactory             $iaEventFactory
     * @param VOFactory                $voFactory
     * @param Notifier                 $notifier
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EventFactory $iaEventFactory,
        VOFactory $voFactory,
        Notifier $notifier,
        LoggerInterface $logger
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->iaEventFactory  = $iaEventFactory;
        $this->voFactory       = $voFactory;
        $this->notifier        = $notifier;
        $this->logger          = $logger;
    }

    /**
     * @param Event $event
     */
    public function userActivated(Event $event)
    {
        $user = $event->getSubject()->getUser();

        // dispatch UserRegistered domain event
        try {
            $emailAddress = $this->voFactory->createEmailAddress($user->getUsername(), EmailAddressType::USERNAME());

            // dispatch user registered event
            $userRegisteredEvent = $this->iaEventFactory->newUserRegistered($user->getGuid(), $emailAddress, $user->getFirstName(), $user->getLastName());
            $this->eventDispatcher->dispatch(UserRegistered::class, $userRegisteredEvent);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Failed dispatching %s event: %s.', UserRegistered::class, $exception->getMessage()));
        }

        // send notifications
        $notification = new RegisterUser($user->getUsername(), array());
        try {
            $this->notifier->sendUserNotification($user->getGuid(), $notification);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Failed sending new user registration notification to %s.', $user->getUsername()), array($exception, $user, $notification));
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
            'workflow.user_account.entered.Active' => 'userActivated'
        ];
    }

}