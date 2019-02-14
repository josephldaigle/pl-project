<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 5:57 PM
 */

namespace PapaLocal\Data\Command\User;

use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\Person;
use PapaLocal\Entity\User;

/**
 * Class FindById.
 */
class FindById extends QueryCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * FindById constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->id = $userId;
    }

    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {

            // fetch user from db view
            $this->tableGateway->setTable('v_user');
            $row = $this->tableGateway->findBy('userId', $this->id);

            // creation code
            if (count($row) < 1) {
                //return empty array if result not found
                throw new UserNotFoundException(sprintf('Unable to locate the user with userId: %s',
                    $this->id));
            }

            // create user object
            $userData = array(
                'id' => $row[0]['userId'],
                'password' => $row[0]['password'],
                'isActive' => $row[0]['isActive'],
                'timeZone' => $row[0]['timeZone'],
                'timeCreated' => $row[0]['userTimeCreated']
            );
            $user = $this->serializer->denormalize($userData, User::class, 'array');

            // create person object
            $personData = array(
                'id' => $row[0]['personId'],
                'firstName' => $row[0]['firstName'],
                'lastName' => $row[0]['lastName'],
                'about' => $row[0]['about'],
                'timeCreated' => $row[0]['personTimeCreated']
            );
            $person = $this->serializer->denormalize($personData, Person::class, 'array');

            // add person and username to user
            $user->setPerson($person);
            $user->setUsername($row[0]['username']);

            // load user roles
            $command = $this->commandFactory->createCommand(LoadUserRoles::class, array('userId' => $user->getId()));
            $roles = $command->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);

            //set roles on user object
            if (false === $roles) {
                $user->setRoles(array());
            } else {
                $user->setRoles($roles);
            }

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