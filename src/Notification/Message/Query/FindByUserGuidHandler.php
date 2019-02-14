<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/12/18
 * Time: 11:25 AM
 */

namespace PapaLocal\Notification\Message\Query;


use PapaLocal\Notification\Data\NotificationRepository;


/**
 * Class FindByUserGuidHandler
 *
 * @package PapaLocal\Notification\Message\Query
 */
class FindByUserGuidHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * FindByUserGuidHandler constructor.
     *
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param FindByUserGuid $query
     *
     * @return \PapaLocal\Entity\Collection\Collection
     */
    public function __invoke(FindByUserGuid $query)
    {
        return $this->notificationRepository->findByUserGuid($query->getUserGuid());
    }

}