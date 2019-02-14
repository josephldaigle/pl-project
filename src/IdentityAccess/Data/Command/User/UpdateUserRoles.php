<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 3:14 PM
 */


namespace PapaLocal\IdentityAccess\Data\Command\User;

use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateUserRoles
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateUserRoles
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var array
     */
    private $roles;

    /**
     * UpdateUserRoles constructor.
     *
     * @param GuidInterface $userGuid
     * @param array         $roles
     */
    public function __construct(GuidInterface $userGuid, array $roles)
    {
        $this->userGuid = $userGuid;
        $this->roles    = $roles;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid->value();
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}