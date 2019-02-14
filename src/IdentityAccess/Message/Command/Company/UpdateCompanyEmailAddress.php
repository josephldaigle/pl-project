<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 7:26 PM
 */


namespace PapaLocal\IdentityAccess\Message\Command\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateCompanyEmailAddress
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyEmailAddress
{
    /**
     * @var string
     */
    private $companyGuid;

    /**
     * @var string
     *
     * @Assert\Email(
     *     message = "The email address is invalid."
     *     )
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     *     )
     *
     * @Assert\Length(
     *     max = 36,
     *     maxMessage = "The email address cannot be longer than {{ limit }} characters."
     * )
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $emailType;

    /**
     * UpdateCompanyEmailAddress constructor.
     *
     * @param string $companyGuid
     * @param string $emailAddress
     * @param string $emailType
     */
    public function __construct($companyGuid, $emailAddress, $emailType)
    {
        $this->companyGuid  = $companyGuid;
        $this->emailAddress = $emailAddress;
        $this->emailType    = $emailType;
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
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getEmailType(): string
    {
        return $this->emailType;
    }

}