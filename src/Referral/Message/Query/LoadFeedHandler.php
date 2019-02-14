<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/27/18
 * Time: 8:35 AM
 */

namespace PapaLocal\Referral\Message\Query;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Feed\Message\Query\LoadFeed;
use PapaLocal\Referral\Data\ReferralRepository;
use PapaLocal\Referral\Entity\Factory\FeedItemFactory;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class LoadFeedHandler
 * @package PapaLocal\Referral\Message\Query
 */
class LoadFeedHandler
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * LoadFeedHandler constructor.
     *
     * @param ReferralRepository $referralRepository
     * @param FeedItemFactory $feedItemFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ReferralRepository $referralRepository,
        FeedItemFactory $feedItemFactory,
        SerializerInterface $serializer
    )
    {
        $this->referralRepository = $referralRepository;
        $this->feedItemFactory    = $feedItemFactory;
        $this->serializer         = $serializer;
    }


    /**
     * TODO: unit test
     * @param LoadFeed $query
     * @return mixed
     */
    public function __invoke(LoadFeed $query)
    {
        if (!in_array("referral", $query->getFeedType())) {
            return [];
        }

        $feedItemsCollection = $this->serializer->denormalize(array(), Collection::class, 'array');

        // load received referral for contact
        $referralWithContactRecipientList = $this->referralRepository->fetchByContactGuid($query->getUser()->getGuid());
        $inboundContactRecipientFeedList = $this->feedItemFactory->generateFeedList($referralWithContactRecipientList);
        $feedItemsCollection->addAll($inboundContactRecipientFeedList);

        // load referrals received for agreements
        $referralWithAgreementRecipientList = $this->referralRepository->fetchByAgreementOwnerGuid($query->getUser()->getGuid());
        $inboundAgreementRecipientFeedList = $this->feedItemFactory->generateFeedList($referralWithAgreementRecipientList);
        $feedItemsCollection->addAll($inboundAgreementRecipientFeedList);

        // load outbound referrals as feedItem
        $referralProvidedList = $this->referralRepository->fetchByProviderGuid($query->getUser()->getGuid());
        $outboundList = $this->feedItemFactory->generateFeedList($referralProvidedList);
        $feedItemsCollection->addAll($outboundList);

        return $feedItemsCollection;
    }
}