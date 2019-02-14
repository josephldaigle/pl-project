<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/21/18
 * Time: 9:12 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumber;


/**
 * Class UpdateCompanyPhone
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyPhone
{
    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * UpdateCompanyPhone constructor.
     *
     * @param GuidInterface $companyGuid
     * @param PhoneNumber   $phoneNumber
     */
    public function __construct(GuidInterface $companyGuid, PhoneNumber $phoneNumber)
    {
        $this->companyGuid = $companyGuid;
        $this->phoneNumber = $phoneNumber;
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
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber->getPhoneNumber();
    }

    /**
     * @return string
     */
    public function getPhoneNumberType(): string
    {
        return $this->phoneNumber->getType()->getValue();
    }
}