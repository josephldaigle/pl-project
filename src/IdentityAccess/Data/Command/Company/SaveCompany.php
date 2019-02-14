<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 10:52 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;

use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Company;


/**
 * Class SaveCompany
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class SaveCompany
{
    /**
     * @var GuidInterface
     */
    private $ownerUserGuid;

    /**
     * @var Company
     */
    private $company;

    /**
     * SaveCompany constructor.
     *
     * @param GuidInterface $ownerUserGuid
     * @param Company       $company
     */
    public function __construct(GuidInterface $ownerUserGuid, Company $company)
    {
        $this->ownerUserGuid = $ownerUserGuid;
        $this->company       = $company;
    }

    /**
     * @return string
     */
    public function getOwnerUserGuid(): string
    {
        return $this->ownerUserGuid->value();
    }

    /**
     * @return string
     */
    public function getCompanyGuid(): string
    {
        return $this->company->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->company->getName();
    }
}