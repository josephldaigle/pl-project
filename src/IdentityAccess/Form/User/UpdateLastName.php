<?php
/**
 * Created by PhpStorm.
 * User: Joe
 * Date: 12/28/18
 * Time: 12:51 PM
 */

namespace PapaLocal\IdentityAccess\Form\User;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateLastName
 *
 * @package PapaLocal\IdentityAccess\Form\User
 */
class UpdateLastName
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     *
     * @Assert\NotBlank(
     *     message = "Name name cannot be blank."
     *     )
     *
     * @Assert\Length(
     *     min = 2,
     *     max = 65,
     *     minMessage = "Name must contain at least two characters.",
     *     maxMessage = "Name cannot contain more than 65 characters."
     *     )
     */
    private $lastName;

    /**
     * UpdateLastName constructor.
     *
     * @param string $userGuid
     * @param string $lastName
     */
    public function __construct($userGuid, $lastName = null)
    {
        $this->userGuid = $userGuid;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getUserGuid(): string
    {
        return $this->userGuid;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}