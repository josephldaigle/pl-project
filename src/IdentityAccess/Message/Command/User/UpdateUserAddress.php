<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/29/18
 * Time: 9:23 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


/**
 * Class UpdateUserAddress
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateUserAddress
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $streetAddress;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $type;

    /**
     * UpdateUserAddress constructor.
     *
     * @param string $guid
     * @param string $streetAddress
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $country
     * @param string $type
     */
    public function __construct(string $guid, string $streetAddress, string $city, string $state, string $postalCode, string $country, string $type)
    {
        $this->guid          = $guid;
        $this->streetAddress = $streetAddress;
        $this->city          = $city;
        $this->state         = $state;
        $this->postalCode    = $postalCode;
        $this->country       = $country;
        $this->type          = $type;
    }


    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}