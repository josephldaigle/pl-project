<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 1:57 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;


/**
 * Class UpdatePhoneNumber
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdatePhoneNumber
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var PhoneNumberType
     */
    private $phoneType;

    /**
     * UpdatePhoneNumber constructor.
     *
     * @param GuidInterface   $userGuid
     * @param string          $phoneNumber
     * @param PhoneNumberType $phoneType
     */
    public function __construct(GuidInterface $userGuid, string $phoneNumber, PhoneNumberType $phoneType)
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
        return $this->userGuid->value();
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
        return $this->phoneType->getValue();
    }
}