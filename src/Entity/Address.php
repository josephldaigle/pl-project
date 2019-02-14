<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/4/17
 */

namespace PapaLocal\Entity;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Address.
 *
 * @package PapaLocal\Entity
 */
class Address extends Entity implements AddressInterface
{
    /**
     * @var int
     *
     * @Assert\Blank(
     *     message = "Id must be blank.",
     *     groups = {"create"}
     *     )
     *
     * @Assert\NotBlank(
     *     message = "Id must be present.",
     *     groups = {"update"}
     *     )
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Street address must be present.",
     *     groups = {"create"}
     * )
     *
     */
    private $streetAddress;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "City must be present.",
     *     groups = {"create"}
     * )
     *
     */
    private $city;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "State must be present.",
     *     groups = {"create"}
     * )
     *
     */
    private $state;

    /**
     * @var string
     *
     */
    private $stateAbbreviated;

    /**
     * Format depends on the country.
     *
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Postal code must be present.",
     *     groups = {"create"}
     * )
     *
     */
    private $postalCode;

    /**
     * @var string full name of country
     *
     * @Assert\NotBlank(
     *     message = "Country must be present.",
     *     groups = {"create"}
     * )
     *
     */
    private $country;

    /**
     * @var string abbreviated name of country
     */
    private $countryAbbreviated;

    /**
     * @var string  the type of address
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time created must be blank.",
     *     groups = {"create"}
     *     )
     */
    private $timeCreated;

    /**
     * @var string
     *
     * @Assert\Blank(
     *     message = "Time updated must be blank.",
     *     groups = {"create"}
     *     )
     */
    private $timeUpdated;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Address
     */
    public function setId(int $id): Address
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @param string $streetAddress
     *
     * @return Address
     */
    public function setStreetAddress(string $streetAddress): Address
    {
        $this->streetAddress = $streetAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(bool $short = false)
    {
        return ($short) ? $this->stateAbbreviated : $this->state;
    }

    /**
     * @param string $state
     *
     * @return Address
     */
    public function setState(string $state): Address
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param string $stateAbbreviated
     *
     * @return Address
     */
    public function setStateAbbreviated(string $stateAbbreviated): Address
    {
        $this->stateAbbreviated = $stateAbbreviated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode(string $postalCode): Address
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry(bool $short = false)
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return Address
     */
    public function setCountry(string $country): Address
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param string $countryAbbreviated
     *
     * @return Address
     */
    public function setCountryAbbreviated(string $countryAbbreviated): Address
    {
        $this->countryAbbreviated = $countryAbbreviated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Address
     */
    public function setType(string $type): Address
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * @param string $timeCreated
     *
     * @return Address
     */
    public function setTimeCreated(string $timeCreated): Address
    {
        $this->timeCreated = $timeCreated;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    /**
     * @param string $timeUpdated
     *
     * @return Address
     */
    public function setTimeUpdated(string $timeUpdated): Address
    {
        $this->timeUpdated = $timeUpdated;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        return sprintf('%s, %s, %s, %s, %s', $this->getStreetAddress(), $this->getCity(),
            $this->getState(), $this->getPostalCode(), $this->getCountry());
    }

    /**
     * @param AddressInterface $address
     * @return bool
     */
    public function isEqualTo(AddressInterface $address)
    {
        return ($this->streetAddress === $address->getStreetAddress()
            && $this->city === $address->getCity()
            && $this->state === $address->getState()
            && $this->postalCode === $address->getPostalCode()
            && $this->country === $address->getCountry());
    }
}
