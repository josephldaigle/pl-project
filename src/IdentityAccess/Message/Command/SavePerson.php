<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Message\Command;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class SavePerson.
 *
 * @package PapaLocal\IdentityAccess\Message\Command
 */
class SavePerson
{
    /**
     * @var GuidInterface
     */
    private $personId;

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
     * @var array
     */
    private $phoneList;

    /**
     * @var array
     */
    private $emailList;

    /**
     * @var array
     */
    private $addressList;

    /**
     * SavePerson constructor.
     *
     * @param GuidInterface $personId
     * @param string        $firstName
     * @param string        $lastName
     * @param string        $about
     */
    public function __construct(
        GuidInterface $personId,
        string $firstName,
        string $lastName,
        string $about = '',
        array $phoneList = [],
        array $emailList = [],
        array $addressList = []
    )
    {
        $this->personId = $personId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->about = $about;
    }

    /**
     * @return GuidInterface
     */
    public function getPersonId(): GuidInterface
    {
        return $this->personId;
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

    /**
     * @return array
     */
    public function getPhoneList(): array
    {
        return $this->phoneList;
    }

    /**
     * @return array
     */
    public function getEmailList(): array
    {
        return $this->emailList;
    }

    /**
     * @return array
     */
    public function getAddressList(): array
    {
        return $this->addressList;
    }
}