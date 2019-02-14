<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:32 AM
 */


namespace PapaLocal\IdentityAccess\Message\Command\User;


/**
 * Class UpdateFirstName
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateFirstName
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     */
    private $firstName;

    /**
     * UpdateFirstName constructor.
     *
     * @param string $userGuid
     * @param string $firstName
     */
    public function __construct(string $userGuid, string $firstName)
    {
        $this->userGuid  = $userGuid;
        $this->firstName = $firstName;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }
}