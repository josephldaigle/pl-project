<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:23 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;

use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateCompanyWebsite
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyWebsite
{
    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * @var string
     */
    private $website;

    /**
     * UpdateCompanyWebsite constructor.
     *
     * @param GuidInterface $companyGuid
     * @param string        $website
     */
    public function __construct(GuidInterface $companyGuid, $website)
    {
        $this->companyGuid = $companyGuid;
        $this->website     = $website;
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
    public function getWebsite(): string
    {
        return $this->website;
    }
}