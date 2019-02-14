<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/24/18
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateCompanyEmail.
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyEmail
{
    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * UpdateCompanyEmail constructor.
     *
     * @param GuidInterface $companyGuid
     * @param EmailAddress  $emailAddress
     */
    public function __construct(GuidInterface $companyGuid, EmailAddress $emailAddress)
    {
        $this->companyGuid = $companyGuid;
        $this->emailAddress = $emailAddress;
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
    public function getEmailAddress(): string
    {
        return $this->emailAddress->getEmailAddress();
    }

    /**
     * @return string
     */
    public function getEmailAddressType(): string
    {
        return $this->emailAddress->getType()->getValue();
    }
}