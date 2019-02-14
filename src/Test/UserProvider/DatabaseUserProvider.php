<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/14/18
 * Time: 3:11 PM
 */

namespace PapaLocal\Test\UserProvider;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class DatabaseUserProvider
 *
 * @package PapaLocal\Test\UserProvider
 */
class DatabaseUserProvider implements UserProviderInterface
{
    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     */
    private $aiMessageFactory;

    /**
     * DatabaseUserProvider constructor.
     *
     * @param MessageBusInterface $appBus
     * @param MessageFactory      $aiMessageFactory
     */
    public function __construct(MessageBusInterface $appBus, MessageFactory $aiMessageFactory)
    {
        $this->appBus           = $appBus;
        $this->aiMessageFactory = $aiMessageFactory;
    }

    /**
     * Loads a user from storage.
     *
     * @param string $username
     * @return mixed
     * @throws \Exception
     */
    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }


    /**
     * Refreshes the user object.
     *
     * @param UserInterface $user
     * @return mixed|UserInterface
     * @throws \Exception
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }


    /**
     * Whether or not the user type provided is supported by this loader.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Exception
     */
    private function fetchUser($username) {

        // fetch user from storage
        $query = $this->aiMessageFactory->newFindUserByUsername($username);
        $user = $this->appBus->dispatch($query);

        // make sure user was found
        if ($user instanceof User) {

            // make sure user has roles
            if (count($user->getRoles()) < 1 || (! is_array($user->getRoles()))) {
                throw new AuthenticationException(sprintf('The user object loaded does not have an array of roles: ',
                    $username));
            }

            if (! in_array('ROLE_USER', $user->getRoles())) {
                throw new AuthenticationException(sprintf('User %s does not have [ROLE_USER].', $username));
            }

            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Unexpected error loading user %s.', $username));
    }
}