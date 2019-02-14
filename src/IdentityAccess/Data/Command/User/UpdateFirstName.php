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
 * Class UpdateFirstName
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateFirstName
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var string
     */
    private $firstName;

    /**
     * UpdateFirstName constructor.
     *
     * @param GuidInterface $userGuid
     * @param string        $firstName
     */
    public function __construct(GuidInterface $userGuid, string $firstName)
    {
        $this->userGuid  = $userGuid;
        $this->firstName = $firstName;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }
}