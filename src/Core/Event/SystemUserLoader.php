<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 8:14 AM
 */

namespace PapaLocal\Core\Event;


use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressFactoryInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class SystemUserLoader
 *
 * @package PapaLocal\Core\Event
 */
class SystemUserLoader implements EventSubscriberInterface
{
    /**
     * @var EmailAddress
     */
    private $adminEmailAddress;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     */
    private $iaMsgFactory;

    /**
     * @var EmailAddressFactoryInterface
     */
    private $voFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SystemUserLoader constructor.
     *
     * @param string                       $adminEmailAddress
     * @param MessageBusInterface          $appBus
     * @param MessageFactory               $iaMsgFactory
     * @param EmailAddressFactoryInterface $voFactory
     * @param LoggerInterface              $logger
     */
    public function __construct(
        string $adminEmailAddress,
        MessageBusInterface $appBus,
        MessageFactory $iaMsgFactory,
        EmailAddressFactoryInterface $voFactory,
        LoggerInterface $logger
    )
    {
        $this->appBus            = $appBus;
        $this->iaMsgFactory      = $iaMsgFactory;
        $this->voFactory         = $voFactory;
        $this->logger            = $logger;
        $this->setAdminEmailAddress($adminEmailAddress);
    }

    /**
     * Loads a user object into the request that contains the sysadmin's user profile.
     *
     * @param GetResponseEvent $event
     */
    public function loadSysAdminProfile(GetResponseEvent $event)
    {
        try {
            // query for user
            $findUserQry = $this->iaMsgFactory->newFindUserByUsername($this->adminEmailAddress->getEmailAddress());
            $user = $this->appBus->dispatch($findUserQry);

            // configure seurity roles
            $roles = (is_array($user->getRoles())) ? $user->getRoles() : [];
            $roles[] = 'ROLE_COMPANY';
            $user->setRoles($roles);

            // add system user to request
            $event->getRequest()->attributes->set('_sysadmin', $user);

        } catch (UserNotFoundException $userNotFoundException) {

            $this->logger->error('The system admin user profile cannot be found. The application should have access to a system user profile in order to carry out automated tasks.');

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An unexpected exception occurred while querying for the system admin user profile: %s.', $exception->getMessage()), array($exception));

        }

        return;
    }

    /**
     * Create the admin's EmailAddress VO.
     *
     * @param string $emailAddress
     *
     * @throws \InvalidArgumentException
     */
    private function setAdminEmailAddress(string $emailAddress)
    {
        if (is_null($emailAddress) || empty($emailAddress)) {
            throw new \InvalidArgumentException(sprintf('The admin email address provided to %s must contain a value.', __CLASS__));
        }

        $this->adminEmailAddress = $this->voFactory->createEmailAddress($emailAddress, EmailAddressType::USERNAME());

    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'loadSysAdminProfile'
        ];
    }

}