<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 6:05 PM
 */


namespace PapaLocal\IdentityAccess\Message\Command\Company;


/**
 * Class UpdateCompanyPhoneNumber
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyPhoneNumber
{
    /**
     * @var string
     */
    private $companyGuid;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $phoneType;

    /**
     * UpdateCompanyPhoneNumber constructor.
     *
     * @param string $companyGuid
     * @param string $phoneNumber
     * @param string $phoneType
     */
    public function __construct(string $companyGuid, string $phoneNumber, string $phoneType)
    {
        $this->companyGuid = $companyGuid;
        $this->phoneNumber = $phoneNumber;
        $this->phoneType   = $phoneType;
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