<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 1:59 PM
 */

namespace PapaLocal\IdentityAccess\Form;


/**
 * Class UpdateAddress
 *
 * @package PapaLocal\IdentityAccess\Form
 */
class UpdateAddress
{
    /**
     * @var string
     */
    private $guid;

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
     * @var string
     */
    private $type;

    /**
     * SaveCompanyAddressForm constructor.
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
    public function getGuid(): string
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