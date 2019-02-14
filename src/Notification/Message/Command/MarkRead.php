<?php
/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 12/4/18
 * Time: 6:01 PM
 */


namespace PapaLocal\Notification\Message\Command;


use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class MarkRead.
 *
 * @package PapaLocal\Notification\Message\Command
 */
class MarkRead
{
    /**
     * @var GuidInterface
     */
    private $notificationGuid;

    /**
     * MarkRead constructor.
     *
     * @param GuidInterface $notificationGuid
     */
    public function __construct(GuidInterface $notificationGuid)
    {
        $this->notificationGuid = $notificationGuid;
    }

    /**
     * @return GuidInterface
     */
    public function getNotificationGuid(): GuidInterface
    {
        return $this->notificationGuid;
    }
}