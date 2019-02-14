<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 2:16 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Entity\User;


/**
 * Class CreateUserAccount
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class CreateUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * CreateUserAccount constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->user->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getPersonGuid(): string
    {
        return $this->user->getPerson()->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->user->getUsername();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->user->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->user->getLastName();
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->user->getPassword();
    }
}