<?php
/**
 * Created by Ewebify, LLC.
 * Date: 10/19/17
 * Time: 5:08 AM
 */


namespace PapaLocal\Controller;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Feed\Entity\Factory\FeedFilterFactory;
use PapaLocal\Feed\Message\MessageFactory;
use PapaLocal\Feed\ParamConverter\FeedFilterParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * FeedController.
 *
 * Provides access to the feed page.
 *
 * @Route("/feed")
 *
 * @package PapaLocal\Controller
 */
class FeedController extends AbstractController
{
    use FormHandlerControllerTrait;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageBusInterface
     */
    private $applicationBus;

    /**
     * FeedController constructor.
     * @param MessageFactory $messageFactory
     * @param MessageBusInterface $applicationBus
     */
    public function __construct(MessageFactory $messageFactory, MessageBusInterface $applicationBus)
    {
        $this->messageFactory = $messageFactory;
        $this->applicationBus = $applicationBus;
    }

    /**
     * Load feed page.
     *
     * @Route("", name="feed", methods={"GET"})
     *
     * @param TokenStorageInterface $tokenStorage
     * @param FeedFilterFactory     $filterFactory
     *
     * @return Response
     */
    public function feedPage(TokenStorageInterface $tokenStorage,
                                FeedFilterFactory $filterFactory
    )
    {
        $user = $tokenStorage->getToken()->getUser();

        // Convert feedFilter form into feedFilter valueObject
        $feedFilter = $filterFactory->createDefault();
        $feedFilter->setTypes(array_merge($feedFilter->getTypes(), ['notification']));

        $query = $this->messageFactory->newLoadFeed($user, $feedFilter->getTypes());
        $feedItems = $this->applicationBus->dispatch($query);

        $feedList = new Collection();

        // Merge items
        foreach ($feedItems as $moduleList) {
            if (count($moduleList) > 0) {
                $feedList->addAll($moduleList->all());
            }
        }

        $query = $this->messageFactory->newApplyFilter($feedList, $feedFilter, 15);
        $filteredItems = $this->applicationBus->dispatch($query);

        //render page
        return $this->render('pages/feed.html.twig', array('items' => $filteredItems, 'feedFilter' => $feedFilter));
    }

    /**
     * Handles post requests to the feed page.
     *
     * @Route("", name="feed_filter", methods={"POST"})
     *
     * @param Request                  $request
     * @param FeedFilterParamConverter $feedFilterParamConverter
     * @param ValidatorInterface       $validator
     * @param FeedFilterFactory        $filterFactory
     * @param TokenStorageInterface    $tokenStorage
     * @param Security                 $security
     *
     * @return Response
     * @throws \Exception
     */
    public function showFilteredPage(Request $request,
                                     FeedFilterParamConverter $feedFilterParamConverter,
                                     ValidatorInterface $validator,
                                     FeedFilterFactory $filterFactory,
                                     TokenStorageInterface $tokenStorage,
                                     Security $security)
    {
        $this->validateFormToken('feed', $request);

        // Convert request data to filterForm
        $feedFilterForm = $feedFilterParamConverter->createFromRequest($request);

        // Validate $feedFilter
        $errors = $validator->validate($feedFilterForm);

        // handle validation errors
        if ($errors->count() > 0) {
            // has errors
            return $this->render('pages/feed.html.twig', array(
                'validationErrors' => $errors,
                'feedFilter' => $filterFactory->createDefault()
            ));
        }

        // Convert feedFilter form into feedFilter valueObject
        $feedFilter = $filterFactory->createFromForm($feedFilterForm);

        // fetch user from storage token
        $user = $tokenStorage->getToken()->getUser();

        $feedItems = [];

        if ($security->isGranted('ROLE_ADMIN')) {
            // user is administrator - could just be loading a regular filter, or chose an item from notification tray
            if ($feedFilterForm->getSelectedItem()) {
                $query = $this->messageFactory->newLoadFeedItem($feedFilterForm->getSelectedItem()->getGuid(), $feedFilterForm->getSelectedItem()->getType());
                $feedItems = $this->applicationBus->dispatch($query);
            }

        } else {
            // user is not administrator
            $query = $this->messageFactory->newLoadFeed($user, $feedFilter->getTypes());
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

        $query = $this->messageFactory->newApplyFilter($feedList, $feedFilter, 15);
        $filteredItems = $this->applicationBus->dispatch($query);

        //render page
        return $this->render('pages/feed.html.twig', [
            'items' => $filteredItems,
            'selectedItem' => (is_null($feedFilterForm->getSelectedItem()) ? [] : $feedFilterForm->getSelectedItem()),
            'feedFilter' => $feedFilter
        ]);
    }
}
