<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/4/18
 * Time: 8:18 AM
 */

namespace PapaLocal\IdentityAccess\ValueObject;


/**
 * Class UserViewFactory
 *
 * @package PapaLocal\IdentityAccess\ValueObject
 */
class UserViewFactory
{
    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return UserView
     */
    public function newUserViewForUser(string $firstName, string $lastName): UserView
    {
        return new UserView($firstName, $lastName);
    }
}