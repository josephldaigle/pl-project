<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/11/18
 * Time: 7:17 PM
 */

namespace PapaLocal\ValueObject\Form;

use Symfony\Component\Validator\Constraints as Assert;
use PapaLocal\Entity\Validation as AppAssert;

/**
 * Class ChangePasswordSuccess.
 *
 * @package PapaLocal\ValueObject\Form
 */
class ResetPassword
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
     *
     * @var string
     *
     * @AppAssert\PasswordConstraint(
     *     message="The password provided is invalid.",
     *     groups = {"authenticated_change"}
     * )
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\Expression(
     *     "this.getPassword() === this.getConfirmPassword()",
     *     message="The password and confirm password must be identical.",
     *     groups = {"authenticated_change"}
     * )
     */
    private $confirmPassword;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return ResetPassword
     */
    public function setUsername(string $username): ResetPassword
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return ResetPassword
     */
    public function setPassword(string $password): ResetPassword
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param string $confirmPassword
     * @return ResetPassword
     */
    public function setConfirmPassword(string $confirmPassword): ResetPassword
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }


}