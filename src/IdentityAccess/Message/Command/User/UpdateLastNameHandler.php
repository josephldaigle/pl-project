<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:35 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateLastNameHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateLastNameHandler
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
     * UpdateLastNameHandler constructor.
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
    public function __invoke(UpdateLastName $command)
    {
        $userGuid = $this->guidFactory->createFromString($command->getUserGuid());
        $updateNameCmd = $this->messageFactory->newUpdateLastName($userGuid, $command->getLastName());
        $this->mysqlBus->dispatch($updateNameCmd);
        return;
    }
}