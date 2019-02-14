<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:19 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


/**
 * Class UpdateCompanyWebsite
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyWebsite
{
    /**
     * @var string
     */
    private $companyGuid;

    /**
     * @var string
     */
    private $website;

    /**
     * UpdateCompanyWebsite constructor.
     *
     * @param string $companyGuid
     * @param string $website
     */
    public function __construct(string $companyGuid, string $website)
    {
        $this->companyGuid = $companyGuid;
        $this->website     = $website;
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
    public function getWebsite(): string
    {
        return $this->website;
    }
}