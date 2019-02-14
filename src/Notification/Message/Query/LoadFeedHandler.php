<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\Message\Query;


use PapaLocal\Feed\Message\Query\LoadFeed;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Notification\ValueObject\FeedItemFactory;
use Symfony\Component\Security\Core\Security;


/**
 * Class LoadFeedHandler.
 *
 * @package PapaLocal\Notification\Message\Query
 */
class LoadFeedHandler
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
     * @var  Security
     */
    private $securityService;

    /**
     * LoadFeedHandler constructor.
     *
     * @param NotificationRepository $notificationRepository
     * @param FeedItemFactory        $feedItemFactory
     * @param Security               $securityService
     */
    public function __construct(NotificationRepository $notificationRepository, FeedItemFactory $feedItemFactory, Security $securityService)
    {
        $this->notificationRepository = $notificationRepository;
        $this->feedItemFactory = $feedItemFactory;
        $this->securityService = $securityService;
    }


    /**
     * @param LoadFeed $query
     *
     * @return mixed
     */
    public function __invoke(LoadFeed $query)
    {
        if (! in_array('notification', $query->getFeedType())) {
            return [];
        }

        // load all user notifications
        $notifications = $this->notificationRepository->findByUserGuid($query->getUser()->getGuid());

        // replace all elements with feed items
        foreach ($notifications as $key => $notification) {
            $notifications->replace($this->feedItemFactory->newUserNotificationItem($notification), $key);
        }

        return $notifications;
    }


}