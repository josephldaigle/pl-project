<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\Message\Command;


use PapaLocal\Notification\Data\NotificationRepository;


/**
 * Class MarkReadHandler.
 *
 * @package PapaLocal\Notification\Message\Command
 */
class MarkReadHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * MarkReadHandler constructor.
     *
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param MarkRead $command
     */
    public function __invoke(MarkRead $command)
    {
        $this->notificationRepository->markRead($command->getNotificationGuid());
    }

}