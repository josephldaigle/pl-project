<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Entity;


use PapaLocal\Core\ValueObject\Guid;


/**
 * Class Person.
 *
 * @package PapaLocal\IdentityAccess\Entity
 */
class Person
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Guid
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
     * @var ContactProfile
     */
    private $contactProfile;

    /**
     * Person constructor.
     *
     * @param Guid                $guid
     * @param string              $firstName
     * @param string              $lastName
     * @param string              $about
     * @param ContactProfile|null $contactProfile
     */
    public function __construct(Guid $guid, string $firstName, string $lastName, string $about = '', ContactProfile $contactProfile = null)
    {
        $this->setGuid($guid);
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setAbout($about);

        if (!is_null($contactProfile)) {
            $this->setContactProfile($contactProfile);
        }

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return Guid
     */
    public function getGuid(): Guid
    {
        return $this->guid;
    }

    /**
     * @param Guid $guid
     */
    public function setGuid(Guid $guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getContactProfile()
    {
        return $this->contactProfile;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getAbout(): string
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout(string $about): void
    {
        $this->about = $about;
    }

    /**
     * @param ContactProfile $contactProfile
     */
    public function setContactProfile(ContactProfile $contactProfile): void
    {
        $this->contactProfile = $contactProfile;
    }
}