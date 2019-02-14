<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 10:18 PM
 */

namespace PapaLocal\IdentityAccess\Event;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class EventFactory
 *
 * @package PapaLocal\IdentityAccess\Event
 */
class EventFactory
{
    /**
     * @param GuidInterface $userGuid
     * @param EmailAddress  $emailAddress
     * @param string        $firstname
     * @param string        $lastname
     *
     * @return UserRegistered
     */
    public function newUserRegistered(GuidInterface $userGuid, EmailAddress $emailAddress, string $firstname, string $lastname): UserRegistered
    {
        return new UserRegistered($userGuid, $emailAddress, $firstname, $lastname);
    }
}