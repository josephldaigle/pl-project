<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 7:27 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateCompanyEmailAddressHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyEmailAddressHandler
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
     * @var VOFactory
     */
    private $voFactory;

    /**
     * UpdateCompanyEmailAddressHandler constructor.
     *
     * @param MessageFactory         $messageFactory
     * @param MessageBusInterface    $mysqlBus
     * @param GuidGeneratorInterface $guidFactory
     * @param VOFactory              $voFactory
     */
    public function __construct(
        MessageFactory $messageFactory,
        MessageBusInterface $mysqlBus,
        GuidGeneratorInterface $guidFactory,
        VOFactory $voFactory
    )
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus       = $mysqlBus;
        $this->guidFactory    = $guidFactory;
        $this->voFactory      = $voFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateCompanyEmailAddress $command)
    {
        // create objects
        $companyGuid = $this->guidFactory->createFromString($command->getCompanyGuid());
        $emailAddress = $this->voFactory->createEmailAddress($command->getEmailAddress(), EmailAddressType::{strtoupper($command->getEmailType())}());

        try {
            $this->mysqlBus->dispatch($this->messageFactory->newStartTransaction());

            $updateEmailCmd = $this->messageFactory->newUpdateCompanyEmailAddress($companyGuid, $emailAddress);
            $this->mysqlBus->dispatch($updateEmailCmd);

            $this->mysqlBus->dispatch($this->messageFactory->newCommitTransaction());

        } catch (\Exception $exception) {
            $this->mysqlBus->dispatch($this->messageFactory->newRollbackTransaction());
            throw $exception;
        }

        return;
    }

}