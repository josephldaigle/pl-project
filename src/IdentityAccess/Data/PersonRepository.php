<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Data;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Service\MessageFactoryInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Entity\Person;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class PersonRepository.
 *
 * @package PapaLocal\IdentityAccess\Data
 */
class PersonRepository extends AbstractRepository
{
    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var MessageFactoryInterface
     */
    private $messageFactory;

    /**
     * PersonRepository constructor.
     *
     * @param MessageBusInterface     $mysqlBus
     * @param MessageFactoryInterface $messageFactory
     */
    public function __construct(
        DataResourcePool $dataResourcePool,
        MessageBusInterface $mysqlBus,
        MessageFactoryInterface $messageFactory
    )
    {
        parent::__construct($dataResourcePool);

        $this->mysqlBus       = $mysqlBus;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param Person        $person
     *
     * @throws \Exception
     */
    public function save(Person $person)
    {
        try {
            dump('person repo save needs to be completed');
            return;
            $this->tableGateway->startTransaction();

            // create new person command
            $savePersonCmd = $this->messageFactory->newSavePerson($person->getId()->value(), $person->getFirstName(), $person->getLastName(), $person->getAbout());
            $this->mysqlBus->dispatch($savePersonCmd);

            // save contact info
            $contactProfile = $person->getContactProfile();
            if ($contactProfile->countEmailAddresses() > 0) {
                $updatePhoneCmd = $this->messageFactory->newUpdatePersonPhoneList($person->getId()->value(), $contactProfile->getPhoneNumberList());
                $this->mysqlBus->dispatch($updatePhoneCmd);
            }

            // foreach email address - create person email address

            // for each address - create person address

            // for each phone number - create person phone number

            $this->tableGateway->commitTransaction();
        } catch (\Exception $exception) {
            $this->tableGateway->rollbackTransaction();

            throw $exception;
        }

    }
}