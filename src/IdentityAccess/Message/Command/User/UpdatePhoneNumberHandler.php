<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/29/18
 * Time: 8:26 AM
 */

namespace PapaLocal\IdentityAccess\Message\Command\User;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdatePhoneNumberHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\User
 */
class UpdatePhoneNumberHandler
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
     * UpdatePhoneNumberHandler constructor.
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
    public function __invoke(UpdatePhoneNumber $command)
    {
        $userGuid = $this->guidFactory->createFromString($command->getUserGuid());

        $updateUserPhoneCmd = $this->messageFactory->newUpdateUserPhoneNumber($userGuid, $command->getPhoneNumber(), PhoneNumberType::{strtoupper($command->getPhoneType())}());

        $this->mysqlBus->dispatch($updateUserPhoneCmd);
        return;
    }

}