<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Entity;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\AddressType;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;


/**
 * Class ContactProfile.
 *
 * @package PapaLocal\IdentityAccess\Entity
 */
class ContactProfile
{
    /**
     * @var Collection
     */
    private $emailList;

    /**
     * @var Collection
     */
    private $addressList;

    /**
     * @var Collection
     */
    private $phoneNumberList;

    /**
     * ContactProfile constructor.
     *
     * @param Collection $emailList
     * @param Collection $addressList
     * @param Collection $phoneNumberList
     */
    public function __construct(Collection $emailList = null,
                                Collection $addressList = null,
                                Collection $phoneNumberList = null)
    {
        $this->emailList = $emailList;
        $this->addressList = $addressList;
        $this->phoneNumberList = $phoneNumberList;
    }

    /**
     * @param Collection $emailList
     *
     * @return ContactProfile
     */
    public function setEmailList(Collection $emailList): ContactProfile
    {
        $this->emailList = $emailList;

        return $this;
    }

    /**
     * @param Collection $addressList
     *
     * @return ContactProfile
     */
    public function setAddressList(Collection $addressList): ContactProfile
    {
        $this->addressList = $addressList;

        return $this;
    }

    /**
     * @param Collection $phoneNumberList
     *
     * @return ContactProfile
     */
    public function setPhoneNumberList(Collection $phoneNumberList): ContactProfile
    {
        $this->phoneNumberList = $phoneNumberList;

        return $this;
    }

    /**
     * @param EmailAddress      $emailAddress
     * @param EmailAddressType  $key
     */
    public function addEmailAddress(EmailAddress $emailAddress, EmailAddressType $key)
    {
        $this->emailList->add($emailAddress, $key);
    }

    /**
     * @param Address       $address
     * @param AddressType   $key
     */
    public function addAddress(Address $address, AddressType $key)
    {
        $this->addressList->add($address, $key);
    }

    /**
     * @param PhoneNumber     $phoneNumber
     * @param PhoneNumberType $key
     */
    public function addPhoneNumber(PhoneNumber $phoneNumber, PhoneNumberType $key)
    {
        $this->phoneNumberList->add($phoneNumber, $key);
    }

    /**
     * @param EmailAddressType $key
     *
     * @return bool
     */
    public function hasEmailAddress(EmailAddressType $key): bool
    {
        return $this->emailList->has($key);
    }

    /**
     * @param AddressType $key
     *
     * @return bool
     */
    public function hasAddress(AddressType $key): bool
    {
        return $this->addressList->has($key);
    }

    /**
     * @param PhoneNumberType $key
     *
     * @return bool
     */
    public function hasPhoneNumber(PhoneNumberType $key): bool
    {
        return $this->phoneNumberList->has($key);
    }

    /**
     * @param EmailAddressType $key
     *
     * @return mixed
     */
    public function getEmailAddress(EmailAddressType $key)
    {
        return $this->emailList->get($key);
    }

    /**
     * @param AddressType $key
     *
     * @return mixed
     */
    public function getAddress(AddressType $key)
    {
        return $this->addressList->get($key);
    }

    /**
     * @param PhoneNumberType $key
     *
     * @return mixed
     */
    public function getPhoneNumber(PhoneNumberType $key)
    {
        return $this->phoneNumberList->get($key);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function findEmailAddressBy($property, $value)
    {
        return $this->emailList->findBy($property, $value);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function findAddressBy($property, $value)
    {
        return $this->addressList->findBy($property, $value);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function findPhoneNumberBy($property, $value)
    {
        return $this->phoneNumberList->findBy($property, $value);
    }

    /**
     * @return Collection
     */
    public function getEmailAddressList()
    {
        return $this->emailList;
    }

    /**
     * @return Collection
     */
    public function getAddressList()
    {
        return $this->addressList;
    }

    /**
     * @return Collection
     */
    public function getPhoneNumberList()
    {
        return $this->phoneNumberList;
    }

    /**
     * @return array
     */
    public function getAllEmailAddresses()
    {
        return $this->emailList->all();
    }

    /**
     * @return array
     */
    public function getAllAddresses()
    {
        return $this->addressList->all();
    }

    /**
     * @return array
     */
    public function getAllPhoneNumbers()
    {
        return $this->phoneNumberList->all();
    }

    /**
     * @return int
     */
    public function countEmailAddresses(): int
    {
        return $this->emailList->count();
    }

    /**
     * @return int
     */
    public function countAddresses(): int
    {
        return $this->addressList->count();
    }

    /**
     * @return int
     */
    public function countPhoneNumbers(): int
    {
        return $this->phoneNumberList->count();
    }
}