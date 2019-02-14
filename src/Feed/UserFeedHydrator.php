<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/17/18
 * Time: 8:28 AM
 */


namespace PapaLocal\Feed;


use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\Notification\Data\PersonNotificationRepository;
use PapaLocal\Notification\Data\NotificationRepository;
use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Entity\Collection\FeedList;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Entity\User;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ValueObject\FeedFilter;
use PapaLocal\ValueObject\User\UserFeed;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * UserFeedHydrator.
 *
 * @package PapaLocal\Feed
 *
 * Hydrator for User feed.
 */
class UserFeedHydrator extends AbstractHydrator
{
    /**
     * @var UserFeed
     */
    private $userFeed;

    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * TODO: Remove when feed is refactored
     *
     * @var ReferralAgreementRepository
     */
    private $referralAgreementRepository;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * @var MessageFactory
     */
    private $raMessageFactory;

    /**
     * UserFeedHydrator constructor.
     *
     * @param TableGateway                $tableGateway
     * @param EntityFactory               $entityFactory
     * @param SerializerInterface         $serializer
     * @param RepositoryRegistry          $repositoryRegistry
     * @param ReferralAgreementRepository $referralAgreementRepository
     * @param MessageBusInterface         $appBus
     * @param MessageFactory              $raMessageFactory
     */
    public function __construct(
        TableGateway $tableGateway,
        EntityFactory $entityFactory,
        SerializerInterface $serializer,
        RepositoryRegistry $repositoryRegistry,
        ReferralAgreementRepository $referralAgreementRepository,
        MessageBusInterface $appBus,
        MessageFactory $raMessageFactory
    )
    {
        parent::__construct($tableGateway, $entityFactory, $serializer);

        $this->repositoryRegistry          = $repositoryRegistry;
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->appBus = $appBus;
        $this->raMessageFactory = $raMessageFactory;
    }

    /**
     * @param Entity $entity
     *
     * @inheritDoc
     */
    public function setEntity(Entity $entity)
    {
        if ( ! $entity instanceof User || ( ! is_numeric($entity->getId()))) {
            throw new \InvalidArgumentException(sprintf('Cannot hydrate instance of %s with %s.',
                get_class($entity), __CLASS__));
        }
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(FeedFilter $filter = null): Entity
    {
        // instantiate a feed card obj
        $feedList       = $this->serializer->denormalize(array(), FeedList::class, 'array');
        $this->userFeed = $this->serializer->denormalize(
            array('feedList' => $feedList),
            UserFeed::class,
            'array');


        // hydrate the feed card obj
        if (is_null($filter)) {
            $this->loadFullFeed();
        } else {
            $this->loadFilteredFeed($filter);
        }

        // set profile on entity and return entity
        $this->entity->setUserFeed($this->userFeed);

        return $this->entity;
    }

    /**
     * Loads the user's feed with filters applied.
     *
     * @param FeedFilter $filter
     */
    private function loadFilteredFeed(FeedFilter $filter)
    {
        //TODO: Refactor to filter feed items.
        $notificationList = $this->repositoryRegistry->get(NotificationRepository::class)
                                                     ->getUserNotifications($this->entity->getId());
        $this->userFeed->getFeedList()->setNotifications($notificationList);
        foreach ($notificationList->all() as $notification) {
            $this->userFeed->addFeedCard($notification);
        }

        // load feed without filtering
//        $this->tableGateway->setTable('v_user_feed');
//        $feedRows = $this->tableGateway->findBy('userId', $this->entity->getId());
//
//        foreach ($feedRows as $row) {
//            $feed = $this->serializer->denormalize($row, Feed::class, 'array');
//            $this->userFeed->addFeedCard($feed);
//        }
    }

    /**
     * Load the user's feed without filters applied.
     */
    private function loadFullFeed()
    {
        // load feed without filtering

        // load notifications
        $notificationList = $this->repositoryRegistry->get(NotificationRepository::class)
                                                     ->getUserNotifications($this->entity->getId());
        foreach ($notificationList->all() as $notification) {
            $this->userFeed->addFeedCard($notification);
        }

        $personNotificationList = $this->repositoryRegistry->get(PersonNotificationRepository::class)
                                                           ->loadNotifications($this->entity->getPerson()->getId());

        foreach ($personNotificationList->all() as $notification) {
            $this->userFeed->addFeedCard($notification);
        }

        // load owned agreements
        $agreementList = $this->referralAgreementRepository->loadUserAgreements($this->entity->getGuid());

        foreach ($agreementList->all() as $agreement) {
            // load invitees
            $this->userFeed->addFeedCard($agreement);
        }

        // load agreements invited to
		$inviteeAgreements = $this->referralAgreementRepository->loadInviteeAgreements($this->entity->getGuid());
		foreach ($inviteeAgreements->all() as $agreement) {
		    $this->userFeed->addFeedCard($agreement);
        }
    }
}