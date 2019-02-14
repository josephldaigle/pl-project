<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:12 PM
 */

namespace PapaLocal\IdentityAccess\Message\Query\User;



/**
 * Class FindUserByUsername
 *
 * @package PapaLocal\IdentityAccess\Message\Query\User
 */
class FindUserByUsername
{
    /**
     * @var string
     */
    private $username;

    /**
     * FindUserByUsername constructor.
     *
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}