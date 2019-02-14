<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\Message\Query;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Feed\Message\Query\LoadFeedItem;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Notification\ValueObject\FeedItemFactory;


/**
 * Class LoadFeedItemHandler.
 *
 * @package PapaLocal\Notification\Message\Query
 */
class LoadFeedItemHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var FeedItemFactory
     */
    private $feedItemFactory;

    /**
     * LoadFeedItemHandler constructor.
     *
     * @param NotificationRepository $notificationRepository
     * @param FeedItemFactory        $feedItemFactory
     */
    public function __construct(NotificationRepository $notificationRepository, FeedItemFactory $feedItemFactory)
    {
        $this->notificationRepository = $notificationRepository;
        $this->feedItemFactory = $feedItemFactory;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LoadFeedItem $query)
    {
        if ('notification' !== $query->getType()) {
            return [];
        }

        $notification = $this->notificationRepository->findByGuid(new Guid($query->getGuid()));

        return $this->feedItemFactory->newUserNotificationItem($notification);
    }


}