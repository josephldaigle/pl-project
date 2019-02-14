<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/16/18
 * Time: 10:14 PM
 */

namespace PapaLocal\IdentityAccess\Event;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use Symfony\Component\EventDispatcher\Event;


/**
 * Class UserRegistered
 *
 * @package PapaLocal\IdentityAccess\Event
 */
class UserRegistered extends Event
{
    /**
     * @var GuidInterface
     */
    private $userGuid;

    /**
     * @var EmailAddress
     */
    private $username;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * UserRegistered constructor.
     *
     * @param GuidInterface $userGuid
     * @param EmailAddress  $username
     * @param string        $firstname
     * @param string        $lastname
     */
    public function __construct(GuidInterface $userGuid, EmailAddress $username, string $firstname, string $lastname)
    {
        $this->userGuid  = $userGuid;
        $this->username  = $username;
        $this->firstname = $firstname;
        $this->lastname  = $lastname;
    }

    /**
     * @return GuidInterface
     */
    public function getUserGuid(): GuidInterface
    {
        return $this->userGuid;
    }

    /**
     * @return EmailAddress
     */
    public function getUsername(): EmailAddress
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastname;
    }
}