<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 9:14 PM
 */

namespace PapaLocal\IdentityAccess\Form\Company;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class CreateCompany
 *
 * @package PapaLocal\IdentityAccess\Form\Company
 */
class CreateCompany
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Company name must be present."
     * )
     *
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Email address cannot be blank."
     * )
     * @Assert\Email(
     *     message = "Email address is not valid."
     * )
     *
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @Assert\Type(
     *     type="numeric",
     *     message="Phone number can contain only numbers."
     * )
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "Phone number must be at exactly {{ limit }} digits long."
     * )
     *
     */
    private $phoneNumber;

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "Address cannot be blank."
     * )
     */
    private $address;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return CreateCompany
     */
    public function setName(string $name): CreateCompany
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $emailAddress
     * @return CreateCompany
     */
    public function setEmailAddress(string $emailAddress): CreateCompany
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     * @return CreateCompany
     */
    public function setPhoneNumber(string $phoneNumber): CreateCompany
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return CreateCompany
     */
    public function setAddress(array $address): CreateCompany
    {
        $this->address = $address;
        return $this;
    }
}