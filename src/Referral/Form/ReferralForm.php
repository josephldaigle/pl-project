<?php
/**
 * Created by eWebify, LLC.
 * User: Yacouba
 * Date: 3/5/18
 * Time: 2:25 PM
 */

namespace PapaLocal\Referral\Form;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Referral\Entity\RecipientInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ReferralForm
 * @package PapaLocal\Referral\ValueObject
 */
class ReferralForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The first name cannot be blank."
     * )
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The last name cannot be blank."
     * )
     */
    private $lastName;

    /**
     * @var PhoneNumber
     *
     * @Assert\NotBlank(
     *     message = "The phone number cannot be blank."
     * )
     *
     * @Assert\Valid()
     */
    private $phoneNumber;

    /**
     * @var EmailAddress
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     * )
     *
     * @Assert\Valid()
     */
    private $emailAddress;

    /**
     * @var Address
     *
     * @Assert\NotBlank(
     *     message = "Please provide an address for this referral."
     * )
     * @Assert\Valid()
     */
    private $address;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide a brief description of this referral."
     * )
     */
    private $about;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Please provide a note this referral recipient.",
     *      groups = {"note"}
     * )
     */
    private $note;

    /**
     * @var RecipientInterface
     *
     * @Assert\NotBlank(
     *     message = "Please provide a recipient for this referral."
     * )
     *
     * @Assert\Valid()
     */
    private $recipient;

    /**
     * ReferralForm constructor.
     * @param string $firstName
     * @param string $lastName
     * @param PhoneNumber $phoneNumber
     * @param EmailAddress $emailAddress
     * @param Address $address
     * @param string $about
     * @param RecipientInterface|null $recipient
     * @param string $note
     */
    public function __construct(string $firstName = '',
                                string $lastName = '',
                                PhoneNumber $phoneNumber = null,
                                EmailAddress $emailAddress = null,
                                Address $address = null,
                                string $about = '',
                                RecipientInterface $recipient = null,
                                string $note = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->emailAddress = $emailAddress;
        $this->address = $address;
        $this->about = $about;
        $this->recipient = $recipient;
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
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

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getAbout(): string
    {
        return $this->about;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return RecipientInterface
     */
    public function getRecipient(): RecipientInterface
    {
        return $this->recipient;
    }

    /**
     * @param RecipientInterface $recipient
     * @return ReferralForm
     */
    public function setRecipient(RecipientInterface $recipient): ReferralForm
    {
        $this->recipient = $recipient;
        return $this;
    }

}