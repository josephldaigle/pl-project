<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/8/18
 * Time: 3:54 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdatePassword
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
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
    private $rawPassword;

    /**
     * UpdatePassword constructor.
     *
     * @param GuidInterface $userGuid
     * @param string        $rawPassword
     */
    public function __construct(GuidInterface $userGuid, $rawPassword)
    {
        $this->userGuid    = $userGuid;
        $this->rawPassword = $rawPassword;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }

    /**
     * @return string   unencoded
     */
    public function getPassword(): string
    {
        return $this->rawPassword;
    }
}