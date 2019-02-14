<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/16/18
 * Time: 8:31 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateCompanyNameHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyNameHandler
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
    private $guidGenerator;

    /**
     * UpdateCompanyNameHandler constructor.
     *
     * @param MessageFactory         $messageFactory
     * @param MessageBusInterface    $mysqlBus
     * @param GuidGeneratorInterface $guidGenerator
     */
    public function __construct(
        MessageFactory $messageFactory,
        MessageBusInterface $mysqlBus,
        GuidGeneratorInterface $guidGenerator
    )
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus       = $mysqlBus;
        $this->guidGenerator  = $guidGenerator;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateCompanyName $command)
    {
        $companyGuid = $this->guidGenerator->createFromString($command->getCompanyGuid());

        $updateNameCommand = $this->messageFactory->newUpdateCompanyName($companyGuid, $command->getName());
        $this->mysqlBus->dispatch($updateNameCommand);

        return;
    }

}