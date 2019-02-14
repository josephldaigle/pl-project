<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/4/18
 * Time: 8:16 AM
 */

namespace PapaLocal\IdentityAccess\ValueObject;


/**
 * Class UserView
 *
 * This class is used to share a view of a user with other users.
 *
 * @package PapaLocal\IdentityAccess\ValueObject
 */
class UserView
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * UserView constructor.
     *
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
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

}