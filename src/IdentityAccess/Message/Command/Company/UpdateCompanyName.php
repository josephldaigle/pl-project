<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 8:30 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateCompanyName
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyName
{
    /**
     * @var string
     */
    private $companyGuid;

    /**
     * @var string
     */
    private $name;

    /**
     * UpdateCompanyName constructor.
     *
     * @param string $companyGuid
     * @param string $name
     */
    public function __construct(string $companyGuid, string $name)
    {
        $this->companyGuid = $companyGuid;
        $this->name        = $name;
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
    public function getName(): string
    {
        return $this->name;
    }
}