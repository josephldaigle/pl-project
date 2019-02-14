<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 7:26 PM
 */

namespace PapaLocal\IdentityAccess\Form\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateCompanyEmail
 *
 * @package PapaLocal\IdentityAccess\Form\Company
 */
class UpdateCompanyEmail
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Company email address must be present."
     * )
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $type;

    /**
     * UpdateCompanyEmail constructor.
     *
     * @param string $guid
     * @param string $emailAddress
     * @param string $type
     */
    public function __construct(string $guid, string $emailAddress = null, string $type)
    {
        $this->guid         = $guid;
        $this->emailAddress = $emailAddress;
        $this->type         = $type;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}