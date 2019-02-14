<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 12/4/18
 * Time: 6:24 PM
 */

namespace PapaLocal\Referral\Message\Query;


use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\ReferralAgreement\Message\MessageFactory as RaMessageFactory;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Feed\Message\Query\LoadFeedItem;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\Entity\Factory\FeedItemFactory;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class LoadFeedItemHandler
 *
 * @package PapaLocal\Referral\Message\Query
 */
class LoadFeedItemHandler
{
    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * @var FeedItemFactory
     */
    private $feedItemFactory;

    /**
     * @var RaMessageFactory
     */
    private $raMessageFactory;

    /**
     * @var MessageBusInterface
     */
    private $appBus;

    /**
     * LoadFeedItemHandler constructor.
     *
     * @param ReferralRepository  $referralRepository
     * @param FeedItemFactory     $feedItemFactory
     * @param RaMessageFactory    $raMessageFactory
     * @param MessageBusInterface $appBus
     */
    public function __construct(
        ReferralRepository $referralRepository,
        FeedItemFactory $feedItemFactory,
        RaMessageFactory $raMessageFactory,
        MessageBusInterface $appBus
    )
    {
        $this->referralRepository = $referralRepository;
        $this->feedItemFactory    = $feedItemFactory;
        $this->raMessageFactory   = $raMessageFactory;
        $this->appBus             = $appBus;
    }

    /**
     * @param LoadFeedItem $query
     *
     * @return array
     */
    public function __invoke(LoadFeedItem $query)
    {
        if ($query->getType() != 'referral') {
            return [];
        }

        $referral = $this->referralRepository->fetchByGuid(new Guid($query->getGuid()));

        $feedItem = $this->feedItemFactory->generateFeedItem($referral);

        if ($referral->getRecipient() instanceof AgreementRecipient) {
            // Query referral agreements
            $findAgreementQuery = $this->raMessageFactory->newFindAgreementByGuid($referral->getRecipient()->getGuid());
            $agreement          = $this->appBus->dispatch($findAgreementQuery);

            // Add the agreement name and bid to feedItem
            $feedItem->setAgreementName($agreement->getName());
            $feedItem->setAgreementBid($agreement->getBid());
        }

        return $feedItem;
    }
}