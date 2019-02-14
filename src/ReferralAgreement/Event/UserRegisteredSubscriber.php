<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 10:57 PM
 */

namespace PapaLocal\ReferralAgreement\Event;


use PapaLocal\IdentityAccess\Event\UserRegistered;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UserRegisteredSubscriber
 *
 * @package PapaLocal\ReferralAgreement\Event
 */
class UserRegisteredSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactory
     */
    private $raMessageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UserRegisteredSubscriber constructor.
     *
     * @param InviteeRepository $inviteeRepository
     * @param MessageBusInterface $mysqlBus
     * @param MessageFactory $raMessageFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        InviteeRepository $inviteeRepository,
        MessageBusInterface $mysqlBus,
        MessageFactory $raMessageFactory,
        LoggerInterface $logger
    )
    {
        $this->inviteeRepository = $inviteeRepository;
        $this->mysqlBus          = $mysqlBus;
        $this->raMessageFactory  = $raMessageFactory;
        $this->logger            = $logger;
    }

    /**
     * Assigns any invitations that have been sent to the user registered, so they will automatically be displayed in the feed.
     *
     * @param UserRegistered $event
     */
    public function handleUserRegistered(UserRegistered $event)
    {
        try {
            $assignGuidCmd = $this->raMessageFactory->newAssignUserGuidToInvitee($event->getUsername()->getEmailAddress(), $event->getUserGuid()->value());

            $this->mysqlBus->dispatch($assignGuidCmd);
        } catch(\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s.', $exception->getLine(), $exception->getFile(), $exception->getMessage()), array('trace' => $exception->getTrace()));
        }

        return;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserRegistered::class => 'handleUserRegistered'
        ];
    }
}