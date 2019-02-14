<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/28/18
 * Time: 10:24 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdatePassword
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdatePassword
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var string
     */
    private $password;

    /**
     * UpdatePassword constructor.
     *
     * @param GuidInterface $userGuid
     * @param string        $password
     */
    public function __construct(GuidInterface $userGuid, string $password)
    {
        $this->userGuid = $userGuid;
        $this->password = $password;
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
    public function getPassword(): string
    {
        return $this->password;
    }
}