<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/5/18
 * Time: 7:36 PM
 */

namespace PapaLocal\ValueObject\Form;


use Symfony\Component\Validator\Constraints as Assert;
use PapaLocal\Entity\Validation as AppAssert;


/**
 * @deprecated since v1.0
 * RegisterUser.
 */
class RegisterUser
{
    /**
     * @var string
     *
     * @Assert\Email(
     *     message="The email address provided is not a valid email."
     * )
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     *     )
     */
    private $username;

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
     * @var string
     *
     * @Assert\Type(
     *     type="numeric",
     *     message="The phone number must be only numbers.",
     * )
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "The phone number must be at exactly {{ limit }} digits long",
     * )
     *
     */
    private $phoneNumber;

    /**
     *
     * @var string
     *
     * @AppAssert\PasswordConstraint(
     *     message="The password provided is invalid."
     * )
     *
     */
    private $password;

    /**
     *
     * @var string
     *
     * @Assert\Expression(
     *     "this.getPassword() === this.getConfirmPassword()",
     *     message="The password and confirm password must be identical."
     * )
     *
     */
    private $confirmPassword;

    /**
     *
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The company name cannot be blank.",
     *     groups = {"userWithCompany"}
     * )
     */
    private $companyName;

    /**
     * @var array
     *
     * @Assert\NotBlank(
     *     message = "The company address cannot be blank.",
     *     groups = {"userWithCompany"}
     * )
     */
    private $companyAddress;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The company phone number cannot be blank.",
     *     groups = {"userWithCompany"}
     * )
     */
    private $companyPhoneNumber;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The company email address cannot be blank.",
     *     groups = {"userWithCompany"}
     * )
     *
     * @Assert\Email(
     *     message="The email address provided is not a valid email.",
     *     groups = {"userWithCompany"}
     * )
     */
    private $companyEmailAddress;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return RegisterUser
     */
    public function setUsername(string $username): RegisterUser
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return RegisterUser
     */
    public function setFirstName(string $firstName): RegisterUser
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return RegisterUser
     */
    public function setLastName(string $lastName): RegisterUser
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return RegisterUser
     */
    public function setPhoneNumber(string $phoneNumber): RegisterUser
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return RegisterUser
     */
    public function setPassword(string $password): RegisterUser
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     * @return RegisterUser
     */
    public function setConfirmPassword(string $confirmPassword): RegisterUser
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return RegisterUser
     */
    public function setCompanyName(string $companyName): RegisterUser
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyAddress()
    {
        return $this->companyAddress;
    }

    /**
     * @param array $companyAddress
     * @return RegisterUser
     */
    public function setCompanyAddress(array $companyAddress): RegisterUser
    {
        $this->companyAddress = $companyAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyPhoneNumber()
    {
        return $this->companyPhoneNumber;
    }

    /**
     * @param string $companyPhoneNumber
     * @return RegisterUser
     */
    public function setCompanyPhoneNumber(string $companyPhoneNumber): RegisterUser
    {
        $this->companyPhoneNumber = $companyPhoneNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyEmailAddress()
    {
        return $this->companyEmailAddress;
    }

    /**
     * @param string $companyEmailAddress
     * @return RegisterUser
     */
    public function setCompanyEmailAddress(string $companyEmailAddress): RegisterUser
    {
        $this->companyEmailAddress = $companyEmailAddress;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCompany(): bool
    {
        return (is_null($this->companyName) || empty($this->companyName)) ? false : true;
    }

}