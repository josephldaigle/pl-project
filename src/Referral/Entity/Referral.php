<?php

/**
 * Created by PhpStorm.
 * Date: 9/10/18
 * Time: 2:42 PM
 */


namespace PapaLocal\Referral\Entity;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Referral\ValueObject\ReferralRating;


/**
 * Class Referral
 * @package PapaLocal\Referral\Entity
 */
class Referral
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
     * @var GuidInterface
     */
    private $providerUserGuid;

    /**
     * @var string
     */
    private $currentPlace;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var PhoneNumber
     */
    private $phoneNumber;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $about;

    /**
     * @var string
     */
    private $note;

    /**
     * @var RecipientInterface
     */
    private $recipient;

    /**
     * @var ReferralRating
     */
    private $rating;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @var string
     */
    private $timeUpdated;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Referral
     */
    public function setId(int $id): Referral
    {
        $this->id = $id;
        return $this;
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
     * @return Referral
     */
    public function setGuid(Guid $guid): Referral
    {
        $this->guid = $guid;
        return $this;
    }

    /**
     * @return GuidInterface
     */
    public function getProviderUserGuid(): GuidInterface
    {
        return $this->providerUserGuid;
    }

    /**
     * @param Guid $providerUserGuid
     * @return Referral
     */
    public function setProviderUserGuid(Guid $providerUserGuid): Referral
    {
        $this->providerUserGuid = $providerUserGuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    /**
     * @param string $currentPlace
     * @return Referral
     */
    public function setCurrentPlace(string $currentPlace): Referral
    {
        $this->currentPlace = $currentPlace;
        return $this;
    }



    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Referral
     */
    public function setFirstName(string $firstName): Referral
    {
        $this->firstName = $firstName;
        return $this;
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
     * @return Referral
     */
    public function setLastName(string $lastName): Referral
    {
        $this->lastName = $lastName;
        return $this;
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
     * @return Referral
     */
    public function setPhoneNumber(PhoneNumber $phoneNumber): Referral
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * @param EmailAddress $emailAddress
     * @return Referral
     */
    public function setEmailAddress(EmailAddress $emailAddress): Referral
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return Referral
     */
    public function setAddress(Address $address): Referral
    {
        $this->address = $address;
        return $this;
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
     * @return Referral
     */
    public function setAbout(string $about): Referral
    {
        $this->about = $about;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return Referral
     */
    public function setNote(string $note): Referral
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param RecipientInterface $recipient
     * @return Referral
     */
    public function setRecipient(RecipientInterface $recipient): Referral
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param ReferralRating $rating
     * @return Referral
     */
    public function setRating(ReferralRating $rating): Referral
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }

    /**
     * @return string
     */
    public function getTimeUpdated(): string
    {
        return $this->timeUpdated;
    }

}