<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 12:22 PM
 */

namespace PapaLocal\IdentityAccess\Entity;


use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\User;


/**
 * Class UserAccount
 *
 * @package PapaLocal\IdentityAccess\Entity
 */
class UserAccount
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * @var Person
     */
    private $person;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var string
     */
    private $currentPlace;

    /***
     * UserAccount constructor.
     *
     * @param string $currentPlace
     */
    public function __construct(string $currentPlace = '')
    {
        $this->setCurrentPlace($currentPlace);
    }

    /**
     * @param User $user
     *
     * @return UserAccount
     */
    public function setUser(User $user): UserAccount
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param Person $person
     *
     * @return UserAccount
     */
    public function setPerson(Person $person): UserAccount
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @param Company $company
     *
     * @return UserAccount
     */
    public function setCompany(Company $company): UserAccount
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @param string $currentPlace
     */
    public function setCurrentPlace(string $currentPlace = '')
    {
        if (empty($currentPlace)) {
            $this->currentPlace = 'Initialized';
        } else {
            $this->currentPlace = $currentPlace;
        }

        return;
    }

    /**
     * @return string
     */
    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @return  bool
     */
    public function hasCompany(): bool
    {
        return ($this->getCompany() instanceof Company);
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return PhoneNumber
     */
    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @param PhoneNumber $phoneNumber
     */
    public function setPhoneNumber(PhoneNumber $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
}