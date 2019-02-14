<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/3/17
 * Time: 9:32 AM
 */

namespace PapaLocal\Data\Command\User;

use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\Entity\User;

/**
 * FindByUsername.
 *
 * Searches for a user by email address, and when found, loads the user's basic detail and user roles.
 */
class FindByUsername extends QueryCommand
{
    /**
     * @var string
     */
    private $username;

    /**
     * FindByUsername constructor.
     *
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            // fetch user from view
            $this->tableGateway->setTable('v_user');
            $userArr = $this->tableGateway->findBy('username', $this->username);

            if (count($userArr) < 1) {
                // throw exception if no user found
                throw new UserNotFoundException(sprintf('Unable to locate an active user with username: %s',
                    $this->username));
            }

            // create user object
            $user = $this->serializer->denormalize($userArr[0], User::class, 'array');
            $user->setId($userArr[0]['userId']);
            $user->setGuid($this->serializer->denormalize(array('value' => $userArr[0]['userGuid']), Guid::class, 'array'));
            $user->setTimeCreated($userArr[0]['userTimeCreated']);

            // create person
            $persArr = array(
                'id' => $userArr[0]['personId'],
                'firstName' => $userArr[0]['firstName'],
                'lastName' => $userArr[0]['lastName'],
                'about' => $userArr[0]['about'],
                'timeCreated' => $userArr[0]['personTimeCreated']);

            //set person and username on user object
            $person = $this->serializer->denormalize($persArr, Person::class, 'array');
            $user->setPerson($person);

            //load user roles
            $loadRolesCommand = $this->commandFactory->createCommand(LoadUserRoles::class, array($user->getId()));
            $roles = $loadRolesCommand->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            //set roles on user object
            $user->setRoles($roles);

            //return user object
            return $user;

        } catch (\Exception $exception) {

            //filter exceptions
            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }
}