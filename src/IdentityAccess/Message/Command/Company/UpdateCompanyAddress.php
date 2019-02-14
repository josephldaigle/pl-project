<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:47 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


/**
 * Class UpdateCompanyAddress
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyAddress
{
    /**
     * @var string
     */
    private $companyGuid;

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
     * UpdateCompanyAddress constructor.
     *
     * @param string $companyGuid
     * @param string $streetAddress
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $country
     * @param string $type
     */
    public function __construct(string $companyGuid, string $streetAddress, string $city, string $state, string $postalCode, string $country, string $type)
    {
        $this->companyGuid   = $companyGuid;
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
    public function getCompanyGuid(): string
    {
        return $this->companyGuid;
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