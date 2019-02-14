<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/29/18
 * Time: 9:23 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\AddressType;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UpdateUserAddressHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateUserAddressHandler
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $mysqlBus;

    /**
     * @var GuidGeneratorInterface
     */
    private $guidFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * UpdateUserAddressHandler constructor.
     *
     * @param MessageFactory         $messageFactory
     * @param MessageBusInterface    $mysqlBus
     * @param GuidGeneratorInterface $guidFactory
     * @param SerializerInterface    $serializer
     */
    public function __construct(
        MessageFactory $messageFactory,
        MessageBusInterface $mysqlBus,
        GuidGeneratorInterface $guidFactory,
        SerializerInterface $serializer
    )
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus       = $mysqlBus;
        $this->guidFactory    = $guidFactory;
        $this->serializer     = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateUserAddress $command)
    {
        // create objects
        $userGuid = $this->guidFactory->createFromString($command->getUserGuid());

        $address = $this->serializer->denormalize([
            'streetAddress' => $command->getStreetAddress(),
            'city' => $command->getCity(),
            'state' => $command->getState(),
            'postalCode' => $command->getPostalCode(),
            'country' => $command->getCountry(),
            'type' => ['value' => $command->getType()]
        ], Address::class, 'array');

        // dispatch command
        $updateUserAddrCmd = $this->messageFactory->newUpdateUserAddress($userGuid, $address);
        $this->mysqlBus->dispatch($updateUserAddrCmd);

        return;
    }


}