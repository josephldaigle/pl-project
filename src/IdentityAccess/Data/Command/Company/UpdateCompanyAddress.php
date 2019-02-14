<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/24/18
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateCompanyAddress.
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyAddress
{
    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * @var Address
     */
    private $address;

    /**
     * UpdateCompanyAddress constructor.
     *
     * @param GuidInterface $companyGuid
     * @param Address       $address
     */
    public function __construct(GuidInterface $companyGuid, Address $address)
    {
        $this->companyGuid = $companyGuid;
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCompanyGuid(): string
    {
        return $this->companyGuid->value();
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