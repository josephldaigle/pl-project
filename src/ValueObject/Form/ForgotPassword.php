<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/5/18
 * Time: 9:54 PM
 */


namespace PapaLocal\ValueObject\Form;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class ForgotPassword
 *
 * @package PapaLocal\ValueObject\Form
 */
class ForgotPassword
{
    /**
     * @var string username
     *
     * @Assert\Email(
     *     message = "The email address provided is not a valid email."
     *     )
     *
     * @Assert\NotBlank(
     *     message = "The email address cannot be blank."
     *     )
     */
    private $username;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return ForgotPassword
     */
    public function setUsername(string $username): ForgotPassword
    {
        $this->username = $username;
        return $this;
    }
}