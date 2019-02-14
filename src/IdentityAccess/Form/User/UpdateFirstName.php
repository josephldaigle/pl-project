<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 12:51 PM
 */

namespace PapaLocal\IdentityAccess\Form\User;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdateFirstName
 *
 * @package PapaLocal\IdentityAccess\Form\User
 */
class UpdateFirstName
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
    private $firstName;

    /**
     * UpdateFirstName constructor.
     *
     * @param $userGuid
     * @param null $firstName
     */
    public function __construct($userGuid, $firstName = null)
    {
        $this->userGuid  = $userGuid;
        $this->firstName = $firstName;
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
    public function getFirstName()
    {
        return $this->firstName;
    }
}