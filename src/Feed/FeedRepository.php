<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 5/24/18
 * Time: 11:25 AM
 */


namespace PapaLocal\Feed;


use PapaLocal\Core\Data\AbstractRepository;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\RepositoryRegistry;
use PapaLocal\Core\Notification\PersonNotification;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Notification;
use PapaLocal\Feed\Form\SelectFeedItemForm;
use PapaLocal\Notification\Data\PersonNotificationRepository;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class FeedRepository
 *
 * @package PapaLocal\Feed
 */
class FeedRepository extends AbstractRepository
{
    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
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
     * @var array mapping of feed item types to their respective domain model.
     */
    private $classMap = [
        'notification'       => Notification::class,
        'personNotification' => PersonNotification::class,
        'referral'           => Referral::class,
    ];

    /**
     * FeedRepository constructor.
     *
     * @param DataResourcePool            $dataResourcePool
     * @param RepositoryRegistry          $repositoryRegistry
     * @param ReferralAgreementRepository $referralAgreementRepository
     */
    public function __construct(
        DataResourcePool $dataResourcePool,
        RepositoryRegistry $repositoryRegistry,
        ReferralAgreementRepository $referralAgreementRepository,
        MessageBusInterface $appBus,
        MessageFactory $raMessageFactory
    )
    {
        parent::__construct($dataResourcePool);

        $this->repositoryRegistry          = $repositoryRegistry;
        $this->referralAgreementRepository = $referralAgreementRepository;
        $this->appBus                      = $appBus;
        $this->raMessageFactory            = $raMessageFactory;
    }

    /**
     * Loads a single feed item.
     *
     * @param SelectFeedItemForm $feedItem
     *
     * @return null
     */
    public function loadFeedItem(SelectFeedItemForm $feedItem)
    {
        $item = null;

        switch ($feedItem->getType()) {
            case 'referralAgreement':
                $agreementGuid = new Guid($feedItem->getId());
                $item = $this->referralAgreementRepository->findByGuid($agreementGuid);

                $inviteeQuery = $this->raMessageFactory->newFindInvitationsByAgreementGuid($agreementGuid);
                $inviteeList = $this->appBus->dispatch($inviteeQuery);

                $item->setInvitees($inviteeList);
                break;
            case 'personNotification':
                $item = $this->repositoryRegistry->get(PersonNotificationRepository::class)
                                                 ->loadNotificationByGuid($feedItem->getId());
                break;
            default:
                $this->tableGateway->setTable('v_feed_detail_'.strtolower($feedItem->getType()));
                $rows = $this->tableGateway->findByGuid($feedItem->getId());

                if (count($rows) < 1) {
                    return null;
                }

                $item = $this->serializer->denormalize($rows[0], $this->classMap[$feedItem->getType()], 'array');
        }

        return $item;
    }
}