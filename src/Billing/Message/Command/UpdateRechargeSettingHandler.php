<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/29/18
 */

namespace PapaLocal\Billing\Message\Command;


use PapaLocal\Billing\Data\MessageFactory;
use PapaLocal\Billing\Notification\ChangeRechargeSetting;
use PapaLocal\Billing\ValueObject\RechargeSetting;
use PapaLocal\Notification\Notifier;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class UpdateRechargeSettingHandler.
 *
 * @package PapaLocal\Billing\Message\Command
 */
class UpdateRechargeSettingHandler
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UpdateRechargeSettingHandler constructor.
     *
     * @param MessageFactory        $messageFactory
     * @param MessageBusInterface   $mysqlBus
     * @param SerializerInterface   $serializer
     * @param Notifier              $notifier
     * @param LoggerInterface       $logger
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        MessageFactory $messageFactory,
        MessageBusInterface $mysqlBus,
        SerializerInterface $serializer,
        Notifier $notifier,
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->messageFactory = $messageFactory;
        $this->mysqlBus       = $mysqlBus;
        $this->serializer     = $serializer;
        $this->notifier       = $notifier;
        $this->logger         = $logger;
        $this->tokenStorage   = $tokenStorage;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(UpdateRechargeSetting $command)
    {
        // create objects
        $rechargeSetting = $this->serializer->denormalize([
            'userGuid'   => ['value' => $command->getUserGuid()],
            'minBalance' => floatval($command->getMinBalance()),
            'maxBalance' => floatval($command->getMaxBalance()),
        ], RechargeSetting::class, 'array');

        // update database
        $updateRechargeSettingCmd = $this->messageFactory->newUpdateRechargeSetting($rechargeSetting->getUserGuid(), $rechargeSetting);
        $this->mysqlBus->dispatch($updateRechargeSettingCmd);

        // send notification ( do not end app exec on failure )
        try {

            // notify user of change to account
            $notification = new ChangeRechargeSetting(
                $rechargeSetting,
                $this->tokenStorage->getToken()->getUser()->getUsername(), array(
                'minBalance' => $rechargeSetting->getMinBalance(),
                'maxBalance' => $rechargeSetting->getMaxBalance(),
            ));
            $this->notifier->sendUserNotification($this->tokenStorage->getToken()->getUser()->getGuid(), $notification);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('A %s occurred at line %s of file %s: %s.', get_class($exception),
                $exception->getLine(), $exception->getFile(), $exception->getMessage()),
                ['trace' => $exception->getTrace()]);
        }


    }


}