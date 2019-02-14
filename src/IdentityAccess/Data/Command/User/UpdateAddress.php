<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/29/18
 * Time: 9:15 AM
 */

namespace PapaLocal\IdentityAccess\Data\Command\User;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateAddress
 *
 * @package PapaLocal\IdentityAccess\Data\Command\User
 */
class UpdateAddress
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var Address
     */
    private $address;

    /**
     * UpdateAddress constructor.
     *
     * @param GuidInterface $userGuid
     * @param Address       $address
     */
    public function __construct(GuidInterface $userGuid, Address $address)
    {
        $this->userGuid = $userGuid;
        $this->address  = $address;
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
    public function getStreetAddress(): string
    {
        return $this->address->getStreetAddress();
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->address->getCity();
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->address->getState();
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->address->getPostalCode();
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->address->getCountry();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->address->getType()->getValue();
    }
}