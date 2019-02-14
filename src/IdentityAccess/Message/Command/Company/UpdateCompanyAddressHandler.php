<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:49 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UpdateCompanyAddressHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyAddressHandler
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
     * UpdateCompanyAddressHandler constructor.
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
    public function __invoke(UpdateCompanyAddress $command)
    {
        // create objects
        $companyGuid = $this->guidFactory->createFromString($command->getCompanyGuid());
        $address = $this->serializer->denormalize([
            'streetAddress' => $command->getStreetAddress(),
            'city' => $command->getCity(),
            'state' => $command->getState(),
            'postalCode' => $command->getPostalCode(),
            'country' => $command->getCountry(),
            'type' => ['value' => $command->getType()]
        ], Address::class, 'array');

        try {
            $this->mysqlBus->dispatch($this->messageFactory->newStartTransaction());

            $updateAddressCmd = $this->messageFactory->newUpdateCompanyAddress($companyGuid, $address);
            $this->mysqlBus->dispatch($updateAddressCmd);

            $this->mysqlBus->dispatch($this->messageFactory->newCommitTransaction());

        } catch (\Exception $exception) {

            $this->mysqlBus->dispatch($this->messageFactory->newRollbackTransaction());
            throw $exception;
        }

        return;
    }

}