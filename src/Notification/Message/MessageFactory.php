<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/12/18
 * Time: 11:22 AM
 */

namespace PapaLocal\Notification\Message;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Notification\Message\Command\MarkRead;
use PapaLocal\Notification\Message\Query\FindByUserGuid;


/**
 * Class MessageFactory
 *
 * @package PapaLocal\Notification\Message
 */
class MessageFactory
{
    /**
     *  COMMANDS
     */

    /**
     * @param GuidInterface $notificationGuid
     *
     * @return MarkRead
     */
    public function newMarkRead(GuidInterface $notificationGuid): MarkRead
    {
        return new MarkRead($notificationGuid);
    }

    /**
     *  QUERIES
     */
    
    /**
     * @param GuidInterface $userGuid
     *
     * @return FindByUserGuid
     */
    public function newFindUserNotifications(GuidInterface $userGuid)
    {
        return new FindByUserGuid($userGuid);
    }
}