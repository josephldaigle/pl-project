<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:41 AM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateLastName
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateLastName
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var string
     */
    private $lastName;

    /**
     * UpdateLastName constructor.
     *
     * @param GuidInterface $userGuid
     * @param string        $lastName
     */
    public function __construct(GuidInterface $userGuid, string $lastName)
    {
        $this->userGuid = $userGuid;
        $this->lastName = $lastName;
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
    public function getLastName(): string
    {
        return $this->lastName;
    }
}