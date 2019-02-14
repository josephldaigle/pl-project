<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Message\Command;


use PapaLocal\IdentityAccess\Entity\ContactProfile;
use PapaLocal\IdentityAccess\Entity\Person;
use PapaLocal\IdentityAccess\Service\PersonService;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class SavePersonHandler.
 *
 * @package PapaLocal\IdentityAccess\Message\Command
 */
class SavePersonHandler
{
    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * SavePersonHandler constructor.
     *
     * @param PersonService       $personService
     * @param SerializerInterface $serializer
     */
    public function __construct(PersonService $personService,
                                SerializerInterface $serializer)
    {
        $this->personService = $personService;
        $this->serializer = $serializer;
    }

    /**
     * @param SavePerson $command
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \PapaLocal\Core\Exception\InvalidStateException
     */
    public function __invoke(SavePerson $command)
    {
        $contactProfile = $this->serializer->denormalize(array(
            'phoneNumberList' => array('items' => $command->getPhoneList()),
            'emailList' => array('items' => $command->getEmailList()),
            'addressList' => array('items' => $command->getAddressList())
        ), ContactProfile::class, 'array');

        // create a person entity
        $person = $this->serializer->denormalize(array(
            'firstName' => $command->getFirstName(),
            'lastName' => $command->getLastName(),
            'about' => $command->getAbout()
        ), Person::class, 'array');

        $person->setContactProfile($contactProfile);

        // call service
        $this->personService->savePerson($command->getPersonId(), $person);

        return;
    }
}