<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/12/18
 * Time: 10:05 PM
 */

namespace PapaLocal\Test\UserProvider;


use PapaLocal\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class InMemoryUserProvider
 *
 * An in-memory user provider that can be used during testing.
 *
 * @package PapaLocal\Test\UserProvider
 */
class InMemoryUserProvider implements UserProviderInterface
{
    private $users;

    /**
     * The user array is a hash where the keys are usernames and the values are
     * an array of attributes: 'password', 'enabled', and 'roles'.
     *
     * @param array $users An array of users
     */
    public function __construct(array $users = array())
    {
        foreach ($users as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $roles = isset($attributes['roles']) ? $attributes['roles'] : array();

            $user = (new User())
                ->setUsername($username)
                ->setPassword($password)
                ->setRoles($roles);

            $this->createUser($user);
        }
    }

    /**
     * Adds a new User to the provider.
     *
     * @throws \LogicException
     */
    public function createUser(UserInterface $user)
    {
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new \LogicException('Another user with the same username already exists.');
        }

        $this->users[strtolower($user->getUsername())] = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->getUser($username);

        $userObj = (new User())
            ->setUsername($user->getUsername())
            ->setPassword($user->getPassword())
            ->setRoles($user->getRoles());

        return $userObj;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $storedUser = $this->getUser($user->getUsername());

        $user = (new User())
            ->setUsername($storedUser->getUsername())
            ->setPassword($storedUser->getPassword())
            ->setRoles($storedUser->getRoles());

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return 'PapaLocal\Entity\User' === $class;
    }

    /**
     * Returns the user by given username.
     *
     * @param string $username The username
     *
     * @return User
     *
     * @throws UsernameNotFoundException if user whose given username does not exist
     */
    private function getUser($username)
    {
        if (!isset($this->users[strtolower($username)])) {
            $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            $ex->setUsername($username);

            throw $ex;
        }

        return $this->users[strtolower($username)];
    }
}