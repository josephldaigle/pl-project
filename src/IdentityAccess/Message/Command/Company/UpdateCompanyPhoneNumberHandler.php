<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/26/18
 * Time: 6:11 PM
 */

namespace PapaLocal\IdentityAccess\Message\Command\Company;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UpdateCompanyPhoneNumberHandler
 *
 * @package PapaLocal\IdentityAccess\Message\Command\Company
 */
class UpdateCompanyPhoneNumberHandler
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
     * UpdateCompanyPhoneNumberHandler constructor.
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
        $this->mysqlBus        = $mysqlBus;
        $this->guidFactory     = $guidFactory;
        $this->voFactory       = $voFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateCompanyPhoneNumber $command)
    {
        // create objects
        $companyGuid = $this->guidFactory->createFromString($command->getCompanyGuid());
        $phoneNumber = $this->voFactory->createPhoneNumber($command->getPhoneNumber(), PhoneNumberType::{strtoupper($command->getPhoneType())}());

        try {
            $this->mysqlBus->dispatch($this->messageFactory->newStartTransaction());

            $command = $this->messageFactory->newUpdateCompanyPhoneNumber($companyGuid, $phoneNumber);
            $this->mysqlBus->dispatch($command);

            $this->mysqlBus->dispatch($this->messageFactory->newCommitTransaction());
        } catch (\Exception $exception) {
            $this->mysqlBus->dispatch($this->messageFactory->newRollbackTransaction());
            throw $exception;
        }
        return;
    }

}