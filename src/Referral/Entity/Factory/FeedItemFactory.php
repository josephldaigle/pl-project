<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/27/18
 * Time: 5:43 PM
 */

namespace PapaLocal\Referral\Entity\Factory;


use PapaLocal\Billing\ValueObject\TransactionTier;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Referral\Entity\FeedItem;
use PapaLocal\Referral\Entity\Referral;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class FeedItemFactory
 * @package PapaLocal\Referral\Entity\Factory
 */
class FeedItemFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * FeedItemFactory constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * TODO: write unit test
     *
     * @param Referral $referral
     * @return mixed
     */
    public function generateFeedItem(Referral $referral)
    {
        $feedItem = $this->serializer->denormalize(array(
            'guid' => $referral->getGuid()->value(),
            'providerUserGuid' => $referral->getProviderUserGuid()->value(),
            'currentPlace' => $referral->getCurrentPlace(),
            'firstName' => $referral->getfirstName(),
            'lastName' => $referral->getLastName(),
            'phoneNumber' => $referral->getPhoneNumber()->getPhoneNumber(),
            'emailAddress' => $referral->getEmailAddress()->getEmailAddress(),
            'about' => $referral->getAbout(),
            'note' => $referral->getNote(),
            'timeCreated' => $referral->getTimeCreated(),
            'timeUpdated' => $referral->getTimeUpdated(),
            'transactionTier' => TransactionTier::TIER_ONE_USER
        ), FeedItem::class, 'array');
        $feedItem->setAddress($referral->getAddress());
        $feedItem->setRecipient($referral->getRecipient());
        $feedItem->setRating($referral->getRating());

        return $feedItem;
    }

    /**
     * TODO: write unit test
     *
     * @param Collection $referralList
     * @return Collection
     */
    public function generateFeedList(Collection $referralList)
    {
        foreach ($referralList as $key => $referral) {
            $feedItem = $this->generateFeedItem($referral);
            $referralList->replace($feedItem, $key);
        }
        $feedItemList = $referralList;

        return $feedItemList;
    }
}