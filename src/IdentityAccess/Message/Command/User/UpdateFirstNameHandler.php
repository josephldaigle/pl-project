<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/28/18
 * Time: 8:32 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateFirstNameHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdateFirstNameHandler
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
     * UpdateFirstNameHandler constructor.
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
    public function __invoke(UpdateFirstName $command)
    {
        $userGuid = $this->guidFactory->createFromString($command->getUserGuid());
        $updateNameCmd = $this->messageFactory->newUpdateFirstName($userGuid, $command->getFirstName());
        $this->mysqlBus->dispatch($updateNameCmd);

        return;
    }

}