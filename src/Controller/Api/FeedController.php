<?php
/**
 * Created by Ewebify, LLC.
 * Date: 12/8/17
 * Time: 7:02 PM
 */


namespace PapaLocal\Controller\Api;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Feed\Entity\Factory\FeedFilterFactory;
use PapaLocal\Feed\Form\FeedFilter;
use PapaLocal\Controller\FormHandlerControllerTrait;
use PapaLocal\Feed\Form\SelectFeedItemForm;
use PapaLocal\Feed\Message\MessageFactory;
use PapaLocal\Feed\ValueObject\NoItemsFoundFeedDetail;
use PapaLocal\Response\RestResponseMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;


/**
 * Class FeedController
 *
 * @package PapaLocal\Controller\Api
 *
 * REST API for Feed domain.
 */
class FeedController extends FOSRestController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var \PapaLocal\ReferralAgreement\Message\MessageFactory
     */
    private $raMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FeedController constructor.
     *
     * @param MessageFactory                                      $messageFactory
     * @param \PapaLocal\ReferralAgreement\Message\MessageFactory $raMessageFactory
     * @param MessageBusInterface                                 $applicationBus
     * @param LoggerInterface                                     $logger
     */
    public function __construct(MessageFactory $messageFactory,
                                \PapaLocal\ReferralAgreement\Message\MessageFactory $raMessageFactory,
                                MessageBusInterface $applicationBus,
                                LoggerInterface $logger)
    {
        $this->messageFactory = $messageFactory;
        $this->raMessageFactory = $raMessageFactory;
        $this->applicationBus = $applicationBus;
        $this->logger = $logger;
    }


    /**
     * Loads a single feed item.
     *
     * @Rest\Post("/feed/item")
     * @ParamConverter("form", class="PapaLocal\Feed\Form\SelectFeedItemForm",
     *     converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param SelectFeedItemForm               $form
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return JsonResponse
     */
    public function selectItem(
        Request $request,
        SelectFeedItemForm $form,
        ConstraintViolationListInterface $validationErrors
    )
    {
        try {
            // check csrf token
            $this->validateFormToken('selectFeedItem', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            $query     = $this->messageFactory->newLoadFeedItem($form->getGuid(), $form->getType());
            $responses = $this->applicationBus->dispatch($query);

            $item = new NoItemsFoundFeedDetail();
            foreach ($responses as $response) {
                if ( ! empty($response)) {
                    $item = $response;
                }
            }

            // load the selected feed item details
            $payload = $this->renderView('fragments/feeds/column2/feedItemDisplay.html.twig', array(
                'item' => $item,
            ));

            return new JsonResponse(array('message' => 'That was easy!', 'item_detail' => $payload),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array('exception' => $exception, 'trace' => $exception->getTrace()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Loads a range of feed items.
     *
     * @Rest\Post("/feed/items/filter")
     * @ParamConverter("form", class="PapaLocal\Feed\Form\FeedFilter", converter="fos_rest.request_body")
     *
     * @param Request                          $request
     * @param FeedFilter                       $form
     * @param ConstraintViolationListInterface $validationErrors
     * @param FeedFilterFactory                $filterFactory
     * @param TokenStorageInterface            $tokenStorage
     * @param Security                         $security
     *
     * @return JsonResponse
     */
    public function fetchFeedItems(Request $request,
                                   FeedFilter $form,
                                   ConstraintViolationListInterface $validationErrors,
                                   FeedFilterFactory $filterFactory,
                                   TokenStorageInterface $tokenStorage,
                                   Security $security)
    {
        try {
            // check csrf token
            $this->validateFormToken('feed', $request);

            // handle validation errors
            if (($response = $this->handleValidationErrors($validationErrors)) instanceof Response) {
                return $response;
            }

            // Convert feedFilter form into feedFilter valueObject
            $feedFilter = $filterFactory->createFromForm($form);

            // fetch user from storage token
            $user = $tokenStorage->getToken()->getUser();

            if ($security->isGranted('ROLE_ADMIN')) {
                // user is administrator
                $query = $this->messageFactory->newLoadFeedItem($form->getSelectedItem()->getGuid(), $form->getSelectedItem()->getType());
                $feedItems = $this->applicationBus->dispatch($query);

            } else {
                // user is not administrator
                $query = $this->messageFactory->newLoadFeed($user, $form->getTypes());
                $feedItems = $this->applicationBus->dispatch($query);
            }

            $feedList = new Collection();

            // Merge items
            foreach ($feedItems as $moduleList) {
                if (count($moduleList) < 1) {
                    continue;
                }

                if ($moduleList instanceof Collection) {
                    $feedList->addAll($moduleList->all());
                } else {
                    $feedList->add($moduleList);
                }
            }

            $query = $this->messageFactory->newApplyFilter($feedList, $feedFilter, ($form->getBeginWith() + $form->getFetchCount()));
            $filteredItems = $this->applicationBus->dispatch($query);

            $responseBody = $this->renderView('fragments/feeds/column1/feedCardCompiler.html.twig', [
                'items' => $filteredItems,
//                'selectedItem' => $filteredItems,
                'feedFilter' => $feedFilter
            ]);

            return new JsonResponse(array('message' => 'That was easy!', 'item_detail' => $responseBody),
                JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {

            // an exception occurred
            $this->logger->error(sprintf('An %s occurred: "%s" at %s line %s', get_class($exception),
                $exception->getMessage(), $exception->getFile(), $exception->getLine()), array('exception' => $exception, 'trace' => $exception->getTrace()));

            return new JsonResponse(array('message' => RestResponseMessage::INTERNAL_SERVER_ERROR),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}