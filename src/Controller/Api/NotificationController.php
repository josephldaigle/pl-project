<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 4/13/18
 */

namespace PapaLocal\Controller\Api;


use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\Entity\Notification\NotificationList;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\IdentityAccess\ValueObject\SystemAdmin;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Notification\Form\MarkReadForm;
use PapaLocal\Notification\Message\MessageFactory;
use PapaLocal\Notification\ValueObject\FeedItemFactory;
use PapaLocal\Response\RestResponseMessage;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;


/**
 * Class NotificationController.
 *
 * @package PapaLocal\Controller\Api
 */
class NotificationController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * NotificationController constructor.
     *
     * @param MessageBusInterface $appBus
     * @param MessageFactory      $messageFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(MessageBusInterface $appBus, MessageFactory $messageFactory, LoggerInterface $logger)
    {
        $this->appBus = $appBus;
        $this->messageFactory = $messageFactory;
        $this->logger = $logger;
    }

    /**
     * @Rest\Post("/account/notification/load")
     *
     * @param Request                $request
     * @param NotificationRepository $notificationRepository
     * @param FeedItemFactory        $feedItemFactory
     * @param TokenStorageInterface  $tokenStorage
     *
     * @return JsonResponse
     */
    public function loadNotificationSet(
        Request $request,
        NotificationRepository $notificationRepository,
        FeedItemFactory $feedItemFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('notificationTray', $request);

            $index = intval($request->request->get('index'));
            $end   = intval($request->request->get('index')) + 14;

            $notificationList = $notificationRepository->getUserNotifications($tokenStorage->getToken()->getUser()->getGuid());

            if ($notificationList->count() > 0) {
                $notificationList->sortByTimeSent(NotificationList::SORT_DESC);
                $finalList = $notificationList->sliceByIndexRange($index, $end);
                $nextItem  = ($finalList->count() < 15) ? -1 : $end + 1;

                foreach ($finalList as $key => $notification) {
                    $finalList->replace($feedItemFactory->newUserNotificationItem($notification), $key);
                }

                return new JsonResponse(array(
                    'payload' => $this->renderView('fragments/notificationTray.html.twig',
                        array('notificationList' => $finalList, 'nextItem' => $nextItem)),
                ),
                    JsonResponse::HTTP_OK);

            } else {
                return new JsonResponse(array(
                    'payload' => $this->renderView('fragments/notificationTray.html.twig',
                        array('notificationList' => $notificationList, 'nextItem' => 0)),
                ),
                    JsonResponse::HTTP_OK);
            }

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/notification/createSavePoint")
     *
     * @param Request                $request
     * @param UserRepository         $userRepository
     * @param TokenStorageInterface  $tokenStorage
     * @param NotificationRepository $notificationRepository
     * @param Security               $security
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function createNotificationSavePoint(
        Request $request,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        NotificationRepository $notificationRepository,
        Security $security
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('notification', $request);

            $notifications         = $notificationRepository->getUserNotifications($tokenStorage->getToken()->getUser()->getGuid());
            $notificationSavePoint = $notifications->count();

            if ($security->isGranted('ROLE_ADMIN')) {
                // load sysadmin notifications
                $notifications = $notificationRepository->findByUserGuid(SystemAdmin::guid());
                foreach ($notifications as $key => $notification) {
                    $notifications->add($notification);
                }
                $notificationSavePoint = $notifications->count();
            }

            $userRepository->createSavePoint($tokenStorage->getToken()->getUser()->getId(), $notificationSavePoint);

            return new JsonResponse(array('message' => 'Notification count saved'), JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/notification/read")
     * @ParamConverter("form", class="PapaLocal\Notification\Form\MarkReadForm", converter="fos_rest.request_body")
     *
     * @param Request                 $request
     * @param MarkReadForm            $form
     * @param ConstraintViolationList $validationErrors
     * @param GuidFactory             $guidFactory
     *
     * @return JsonResponse
     */
    public function markNotificationRead(Request $request,
                                         MarkReadForm $form,
                                         ConstraintViolationList $validationErrors,
                                         GuidFactory $guidFactory
    )
    {
        try {
            // validate CSRF token
            $this->validateFormToken('markNotificationRead', $request);

            // validate form inputs
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $notificationGuid = $guidFactory->createFromString($form->getNotificationGuid());
            $markReadCmd = $this->messageFactory->newMarkRead($notificationGuid);
            $this->appBus->dispatch($markReadCmd);

            return new JsonResponse(['message' => "Notification was marked as read."], JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            $this->logger->error(sprintf('An exception occurred at line %s of file %s: %s', $exception->getLine(),
                $exception->getFile(), $exception->getMessage()), array($exception));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}