<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/12/18
 */

namespace PapaLocal\Notification\ValueObject;


use PapaLocal\Notification\Entity\UserNotification;


/**
 * Class FeedItemFactory.
 *
 * @package PapaLocal\Notification\ValueObject
 */
class FeedItemFactory
{
    /**
     * @param UserNotification $userNotification
     *
     * @return UserNotificationFeedItem
     */
    public function newUserNotificationItem(UserNotification $userNotification)
    {
        return new UserNotificationFeedItem($userNotification);
    }
}