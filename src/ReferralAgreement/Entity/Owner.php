<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/17/18
 */


namespace PapaLocal\ReferralAgreement\Entity;


use PapaLocal\Entity\PersonInterface;
use PapaLocal\Entity\UserInterface;


/**
 * Class Owner
 *
 * Model a referral agreement owner.
 *
 * @package PapaLocal\ReferralAgreement\Entity
 */
class Owner implements OwnerInterface
{
    /**
     * @var PersonInterface
     */
    private $person;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param PersonInterface $person
     * @return Owner
     */
    public function setPerson(PersonInterface $person): Owner
    {
        $this->person = $person;
        return $this;
    }

    /**
     * @param UserInterface $user
     * @return Owner
     */
    public function setUser(UserInterface $user): Owner
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstName()
    {
        return $this->person->getFirstName();
    }

    /**
     * @inheritdoc
     */
    public function getLastName()
    {
        return $this->person->getLastName();
    }

    /**
     * @inheritdoc
     */
    public function getAbout()
    {
        return 'Not available.';
    }

    /**
     * @return int
     */
    public function getUserId()
    {
       return $this->user->getGuid();
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->user->getUsername();
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return 'Not available.';
    }

    /**
     * @inheritdoc
     */
    public function getTimeZone()
    {
        return $this->user->getTimeZone();
    }

    /**
     * @inheritdoc
     */
    public function getIsActive(): bool
    {
        return $this->user->getIsActive();
    }
}