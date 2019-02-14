<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\Core\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;



/**
 * Class Address.
 *
 * @package PapaLocal\Core\ValueObject
 */
class Address
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Street address must be present."
     * )
     *
     */
    private $streetAddress;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "City must be present."
     * )
     *
     */
    private $city;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "State must be present."
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
     *     message = "Postal code must be present."
     * )
     */
    private $postalCode;

    /**
     * @var string full name of country
     *
     * @Assert\NotBlank(
     *     message = "Country must be present."
     * )
     *
     */
    private $country;

    /**
     * @var string abbreviated name of country
     */
    private $countryAbbreviated;

    /**
     * @var AddressType
     */
    private $type;

    /**
     * Address constructor.
     *
     * @param string      $streetAddress
     * @param string      $city
     * @param string      $state
     * @param string      $postalCode
     * @param string      $country
     * @param AddressType $type
     * @param string      $stateAbbreviated
     * @param string      $countryAbbreviated
     *
     */
    public function __construct(string $streetAddress,
                                string $city,
                                string $state,
                                string $postalCode,
                                string $country,
                                AddressType $type = null,
                                string $stateAbbreviated = '',
                                string $countryAbbreviated = ''
    )
    {
        $this->streetAddress = $streetAddress;
        $this->city = $city;
        $this->state = $state;
        $this->stateAbbreviated = $stateAbbreviated;
        $this->postalCode = $postalCode;
        $this->country = $country;
        $this->countryAbbreviated = $countryAbbreviated;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
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
    public function getCity(): string
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
    public function getState(): string
    {
        return $this->state;
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
     * @return string
     */
    public function getStateAbbreviated(): string
    {
        return $this->stateAbbreviated;
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
     * @return string
     */
    public function getPostalCode(): string
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
     * @return string
     */
    public function getCountry(): string
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
     * @return string
     */
    public function getCountryAbbreviated(): string
    {
        return $this->countryAbbreviated;
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
     * @param AddressType $type
     *
     * @return Address
     */
    public function setType(AddressType $type): Address
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return sprintf('%s, %s, %s, %s, %s', $this->getStreetAddress(), $this->getCity(),
            $this->getState(), $this->getPostalCode(), $this->getCountry());
    }
}