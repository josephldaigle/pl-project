<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 1:58 PM
 */

namespace PapaLocal\IdentityAccess\Form\User;


use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class UpdatePhoneNumber
 *
 * @package PapaLocal\IdentityAccess\Form\User
 */
class UpdatePhoneNumber
{
    /**
     * @var string
     */
    private $userGuid;

    /**
     * @var string
     *
     * @Assert\Type(
     *     type = "numeric",
     *     message = "The phone number can only contain numbers."
     * )
     *
     * @Assert\Regex(
     *     pattern = "/^[1-9]/",
     *     match = true,
     *     message = "The phone number cannot begin with zero."
     * )
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      exactMessage = "The phone number must be exactly {{ limit }} digits long."
     * )
     *
     * @Assert\NotNull(
     *     message = "Phone number must be present."
     * )
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $phoneType;

    /**
     * UpdatePhoneNumber constructor.
     *
     * @param string $userGuid
     * @param string $phoneNumber
     * @param string $phoneType
     */
    public function __construct($userGuid, $phoneNumber = null, $phoneType)
    {
        $this->userGuid    = $userGuid;
        $this->phoneNumber = $phoneNumber;
        $this->phoneType   = $phoneType;
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
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getPhoneType(): string
    {
        return $this->phoneType;
    }

}