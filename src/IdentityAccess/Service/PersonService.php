<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Service;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Data\PersonRepository;
use PapaLocal\IdentityAccess\Entity\Person;


/**
 * Class PersonService.
 *
 * @package PapaLocal\IdentityAccess\Service
 */
class PersonService
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * Creates a new Person.
     *
     * @param Person        $person
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \PapaLocal\Core\Exception\InvalidStateException
     */
    public function savePerson(Person $person)
    {
        $this->personRepository->save( $person);

        return;
    }


    public function findAll()
    {
        // TODO: Implement and replace PapaLocal\Data\Repository\Person\PersonRepository::loadAll
    }
}