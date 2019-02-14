<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 5/4/18
 */

namespace PapaLocal\Controller;


use PapaLocal\IdentityAccess\ValueObject\SystemAdmin;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\Notification\ValueObject\FeedItemFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


/**
 * Class NotificationController.
 *
 * @package PapaLocal\Controller
 *
 * Provides page-loading actions for the Notification domain.
 */
class NotificationController extends AbstractController
{
    /**
     * Fetches the first 15 user notifications for the page template.
     *
     * @Route("/notification/user/all", name="notification_user_all", methods={"GET"})
     *
     * @param NotificationRepository $notificationRepository
     * @param TokenStorageInterface  $tokenStorage
     * @param FeedItemFactory        $feedItemFactory
     * @param Security               $security
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fetchUserNotifications(
        NotificationRepository $notificationRepository,
        TokenStorageInterface $tokenStorage,
        FeedItemFactory $feedItemFactory,
        Security $security
    )
    {
        // load all notifications
        $notifications = $notificationRepository->getUserNotifications($tokenStorage->getToken()->getUser()->getGuid());

        // load admin notifications for admin users
        if ($security->isGranted('ROLE_ADMIN')) {
            // load sysadmin notifications
            $adminNotifications = $notificationRepository->findByUserGuid(SystemAdmin::guid());

            foreach ($adminNotifications as $key => $notification) {
                $notifications->add($notification);
            }
        }

        // TODO: apply feed filters and sort to this collection
        // slice last 15 notifications
        if ($notifications->count() > 0) {
            $notifications->sortByTimeSent(NotificationList::SORT_DESC);
            $finalList = $notifications->sliceByIndexRange(0, 14);
            $nextItem  = ($notifications->count() <= 15) ? -1 : 15;

            // convert each item into feed item
            foreach ($finalList as $key => $notification) {

                $feedItem = $feedItemFactory->newUserNotificationItem($notification);

                $finalList->replace($feedItem, $key);
            }

            return $this->render('fragments/notificationTray.html.twig',
                array('notificationList' => $finalList, 'nextItem' => $nextItem));
        } else {
            // notification list is empty
            return $this->render('fragments/notificationTray.html.twig',
                array('notificationList' => $notifications, 'nextItem' => 0));
        }
    }

    /**
     * @Route("/notification/count/unread", name="notification_count_unread", methods={"GET"})
     *
     * @param NotificationRepository $notificationRepository
     * @param TokenStorageInterface $tokenStorage
     * @param Security $security
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countUserUnreadNotifications(
        NotificationRepository $notificationRepository,
        TokenStorageInterface $tokenStorage,
        Security $security
    )
    {
        $notifications = $notificationRepository->getUserNotifications($tokenStorage->getToken()->getUser()->getGuid());

        if ($security->isGranted('ROLE_ADMIN')) {
            // load sysadmin notifications
            $notifications = $notificationRepository->findByUserGuid(SystemAdmin::guid());

            foreach ($notifications as $key => $notification) {
                $notifications->add($notification);
            }
        }

        // Compute the amount of new notifications
        $newNotifications = $notifications->count() - $tokenStorage->getToken()->getUser()->getnotificationSavePoint();

        return $this->render('fragments/notificationCount.html.twig', array('notificationCount' => $newNotifications));
    }
}