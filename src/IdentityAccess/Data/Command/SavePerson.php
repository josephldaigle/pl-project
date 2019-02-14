<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Data\Command;


/**
 * Class SavePerson.
 *
 * @package PapaLocal\IdentityAccess\Data\Command
 */
class SavePerson
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $about;

    /**
     * SavePerson constructor.
     *
     * @param string $guid
     * @param string $firstName
     * @param string $lastName
     * @param string $about
     */
    public function __construct(string $guid, string $firstName, string $lastName, string $about = '')
    {
        $this->guid = $guid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->about = $about;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
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

    /**
     * @return string
     */
    public function getAbout(): string
    {
        return $this->about;
    }
}