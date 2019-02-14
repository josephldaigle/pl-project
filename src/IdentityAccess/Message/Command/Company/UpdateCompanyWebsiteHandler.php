<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 8:19 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateCompanyWebsiteHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyWebsiteHandler
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
     * UpdateCompanyWebsiteHandler constructor.
     *
     * @param MessageFactory         $messageFactory
     * @param MessageBusInterface    $mysqlBus
     * @param GuidGeneratorInterface $guidFactory
     */
    public function __construct(
        MessageFactory $messageFactory,
        MessageBusInterface $mysqlBus,
        GuidGeneratorInterface $guidFactory
    )
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus       = $mysqlBus;
        $this->guidFactory    = $guidFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateCompanyWebsite $command)
    {
        // convert objects
        $companyGuid = $this->guidFactory->createFromString($command->getCompanyGuid());
        $updateWebsiteCmd = $this->messageFactory->newUpdateCompanyWebsite($companyGuid, $command->getWebsite());
        $this->mysqlBus->dispatch($updateWebsiteCmd);
    }
}