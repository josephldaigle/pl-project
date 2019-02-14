<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 11:06 AM
 */


namespace PapaLocal\Core\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class EmailAddress
 *
 * @package PapaLocal\Core\ValueObject
 */
class EmailAddress implements EmailAddressInterface
{
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
     * @var EmailAddressType
     */
    private $type;

    /**
     * EmailAddress constructor.
     *
     * @param string           $emailAddress
     * @param EmailAddressType $type
     */
    public function __construct(
        string $emailAddress,
        EmailAddressType $type)
    {
        $this->setEmailAddress($emailAddress);
        $this->setType($type);
    }

    /**
     * @param string $emailAddress
     */
    protected function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param EmailAddressType $type
     */
    protected function setType(EmailAddressType $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return EmailAddressType
     */
    public function getType(): EmailAddressType
    {
        return $this->type;
    }
}