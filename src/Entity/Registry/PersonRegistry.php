<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/16/18
 */

namespace PapaLocal\Entity\Registry;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Person;


/**
 * Class PersonRegistry.
 *
 * @package PapaLocal\Entity\Registry
 *
 * Wraps a Collection, providing functions specific to Person objects.
 */
class PersonRegistry
{
    /**
     * @var Collection
     */
    private $personList;

    /**
     * @param Collection $personList
     * @return PersonRegistry
     */
    public function setPersonList(Collection $personList): PersonRegistry
    {
        $this->personList = $personList;
        return $this;
    }

    /**
     * Fetch a Person by their id.
     *
     * @param int $id
     * @return mixed
     */
    public function fetchById(int $id)
    {
        $personIter = $this->personList->getIterator();

        foreach ($personIter as $key => $person) {
            if (! $person instanceof Person) {
                throw new \LogicException(sprintf('PersonRegistry should not be used to wrap a collection whose underlying items are not instances of %s', Person::class));
            }

            if ($person->getId() == $id) {
                return $person;
            }
        }

        return null;
    }

    /**
     * Check if a person exists.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $emailAddress
     * @return bool | int person's id or false if not found
     */
    public function personExists(string $firstName, string $lastName, string $emailAddress)
    {
        foreach ($this->personList->all() as $person) {
            $emailAddresses = $person->getContactProfile()->getEmailAddressList();
            $emailMatch = (is_null($emailAddresses->findBy('emailAddress', $emailAddress))) ? false : true;

            $nameMatch = (strcasecmp($person->getFirstName(), $firstName) == 0
                && strcasecmp($person->getLastName(), $lastName) == 0) ? true : false;

            if ($emailMatch && $nameMatch) {
                return (int)$person->getId();
            }
        }

        return false;
    }
}