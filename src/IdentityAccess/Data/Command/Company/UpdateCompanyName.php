<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 7:13 PM
 */

namespace PapaLocal\IdentityAccess\Data\Command\Company;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class UpdateCompanyName
 *
 * @package PapaLocal\IdentityAccess\Data\Command\Company
 */
class UpdateCompanyName
{
    /**
     * @var GuidInterface
     */
    private $companyGuid;

    /**
     * @var string
     */
    private $name;

    /**
     * UpdateCompanyName constructor.
     *
     * @param GuidInterface $companyGuid
     * @param string        $name
     */
    public function __construct(GuidInterface $companyGuid, string $name)
    {
        $this->companyGuid = $companyGuid;
        $this->name        = $name;
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
    public function getName(): string
    {
        return $this->name;
    }
}