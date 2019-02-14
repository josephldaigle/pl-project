<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/16/18
 * Time: 10:24 PM
 */

namespace PapaLocal\ValueObject;

use PapaLocal\Entity\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\PhoneNumber;

/**
 * ContactProfile.
 *
 * @package PapaLocal\ValueObject
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
    public function __construct(Collection $emailList, Collection $addressList, Collection $phoneNumberList)
    {
        $this->emailList = $emailList;
        $this->addressList = $addressList;
        $this->phoneNumberList = $phoneNumberList;
    }

    /**
     * @param EmailAddress $emailAddress
     * @param string       $key
     */
    public function addEmailAddress(EmailAddress $emailAddress, string $key = null)
    {
        $this->emailList->add($emailAddress, $key);
    }

    /**
     * @param Address $address
     * @param string  $key
     */
    public function addAddress(Address $address, string $key = null)
    {
        $this->addressList->add($address, $key);
    }

    /**
     * @param PhoneNumber $phoneNumber
     * @param string      $key
     */
    public function addPhoneNumber(PhoneNumber $phoneNumber, string $key = null)
    {
        $this->phoneNumberList->add($phoneNumber, $key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasEmailAddress(string $key)
    {
        return $this->emailList->has($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAddress(string $key)
    {
        return $this->addressList->has($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasPhoneNumber(string $key)
    {
        return $this->phoneNumberList->has($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getEmailAddress(string $key)
    {
        return $this->emailList->get($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAddress(string $key)
    {
        return $this->addressList->get($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getPhoneNumber(string $key)
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