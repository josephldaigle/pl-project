<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 10:15 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateUsername
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateUsername
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var string
     */
    private $username;

    /**
     * UpdateUsername constructor.
     *
     * @param GuidInterface $userGuid
     * @param string        $username
     */
    public function __construct(GuidInterface $userGuid, string $username)
    {
        $this->userGuid = $userGuid;
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid->value();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}