<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:34 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


/**
 * Class UpdateLastName
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateLastName
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     */
    private $lastName;

    /**
     * UpdateLastNameHandler constructor.
     *
     * @param string $userGuid
     * @param string $lastName
     */
    public function __construct(string $userGuid, string $lastName)
    {
        $this->userGuid = $userGuid;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
}