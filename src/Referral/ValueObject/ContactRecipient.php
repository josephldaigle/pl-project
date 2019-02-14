<?php
/**
 * Created by PhpStorm.
 * Date: 9/11/18
 * Time: 1:50 PM
 */

namespace PapaLocal\Referral\ValueObject;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Referral\Entity\RecipientInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ContactRecipient
 * @package PapaLocal\Referral\ValueObject
 */
class ContactRecipient implements RecipientInterface
{
    /**
     * @var GuidInterface
     *
     * @Assert\Valid()
     */
    private $contactGuid;
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide the recipient's first name.",
     *     groups = {"contact"}
     * )
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide the recipient's last name.",
     *     groups = {"contact"}
     * )
     *
     */
    private $lastName;

    /**
     * @var PhoneNumber
     *
     * @Assert\NotBlank(
     *     message = "Please provide the recipient's phone number.",
     *     groups = {"contact"}
     * )
     *
     * @Assert\Valid()
     */
    private $phoneNumber;

    /**
     * @var EmailAddress
     *
     * @Assert\NotBlank(
     *     message = "Please provide the recipient's email address.",
     *     groups = {"contact"}
     * )
     *
     * @Assert\Valid()
     */
    private $emailAddress;

    /**
     * ContactRecipient constructor.
     * @param string $firstName
     * @param string $lastName
     * @param PhoneNumber $phoneNumber
     * @param EmailAddress $emailAddress
     * @param Guid|null $contactGuid
     */
    public function __construct(string $firstName = '',
                                string $lastName = '',
                                PhoneNumber $phoneNumber = null,
                                EmailAddress $emailAddress = null,
                                Guid $contactGuid = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->emailAddress = $emailAddress;
        $this->contactGuid = $contactGuid;
    }

    /**
     * @return mixed
     */
    public function getContactGuid()
    {
        return $this->contactGuid;
    }

    /**
     * @param GuidInterface $contactGuid
     * @return ContactRecipient
     */
    public function setContactGuid(GuidInterface $contactGuid): ContactRecipient
    {
        $this->contactGuid = $contactGuid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
}