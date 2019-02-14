<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/22/18
 * Time: 11:42 AM
 */

namespace PapaLocal\IdentityAccess\Form;


use Symfony\Component\Validator\Constraints as Assert;
use PapaLocal\Entity\Validation as AppAssert;


/**
 * Class CreateUserAccountForm
 *
 * @package PapaLocal\IdentityAccess\Form
 */
class CreateUserAccountForm
{
    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     * )
     *
     * @Assert\Email(
     *     message="The email address provided is not a valid email."
     * )
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
     * CreateUserAccountForm constructor.
     *
     * @param string $username
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     * @param string $password
     * @param string $confirmPassword
     */
    public function __construct(string $username = '',
                                string $firstName = '',
                                string $lastName = '',
                                string $phoneNumber = '',
                                string $password = '',
                                string $confirmPassword = '')
    {
        $this->username        = $username;
        $this->firstName       = $firstName;
        $this->lastName        = $lastName;
        $this->phoneNumber     = $phoneNumber;
        $this->password        = $password;
        $this->confirmPassword = $confirmPassword;
    }


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
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
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
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
     * @return CreateUserAccountForm
     */
    public function setCompanyName(string $companyName): CreateUserAccountForm
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
     * @return CreateUserAccountForm
     */
    public function setCompanyAddress(array $companyAddress): CreateUserAccountForm
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
     * @return CreateUserAccountForm
     */
    public function setCompanyPhoneNumber(string $companyPhoneNumber): CreateUserAccountForm
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
     * @return CreateUserAccountForm
     */
    public function setCompanyEmailAddress(string $companyEmailAddress): CreateUserAccountForm
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