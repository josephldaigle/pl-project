<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/29/18
 * Time: 8:26 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


/**
 * Class UpdatePhoneNumber
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdatePhoneNumber
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $phoneType;

    /**
     * UpdatePhoneNumber constructor.
     *
     * @param string $userGuid
     * @param string $phoneNumber
     * @param string $phoneType
     */
    public function __construct($userGuid, $phoneNumber, $phoneType)
    {
        $this->userGuid    = $userGuid;
        $this->phoneNumber = $phoneNumber;
        $this->phoneType   = $phoneType;
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
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneType(): string
    {
        return $this->phoneType;
    }
}