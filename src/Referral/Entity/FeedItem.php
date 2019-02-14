<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/27/18
 * Time: 11:13 AM
 */

namespace PapaLocal\Referral\Entity;


use PapaLocal\Billing\ValueObject\TransactionTier;
use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\FeedItemInterface;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ReferralRating;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use Symfony\Component\Messenger\MessageBus;

/**
 * Class FeedItem
 * @package PapaLocal\Referral\Entity
 */
class FeedItem implements FeedItemInterface
{
    /**
     * @var string
     */
    private $guid;

    /**
     * @var string
     */
    private $providerUserGuid;

    /**
     * @var string
     */
    private $title = 'Referral';

    /**
     * @var string
     */
    private $feedType = 'referral';

    /**
     * @var string
     */
    private $currentPlace;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $about;

    /**
     * @var string
     */
    private $note;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var RecipientInterface
     */
    private $recipient;

    /**
     * @var string
     */
    private $agreementName;

    /**
     * @var double
     */
    private $agreementBid;

    /**
     * @var ReferralRating
     */
    private $rating;

    /**
     * @var string
     */
    private $timeCreated;

    /**
     * @var string
     */
    private $timeUpdated;

    /**
     * @var TransactionTier
     */
    private $transactionTier;

    /**
     * FeedItem constructor.
     * @param string $guid
     * @param string $providerUserGuid
     * @param string $currentPlace
     * @param string $firstName
     * @param string $lastName
     * @param string $phoneNumber
     * @param string $emailAddress
     * @param string $about
     * @param string $note
     * @param string $timeCreated
     * @param string $timeUpdated
     */
    public function __construct(string $guid,
                                string $providerUserGuid,
                                string $currentPlace,
                                string $firstName,
                                string $lastName,
                                string $phoneNumber,
                                string $emailAddress,
                                string $about,
                                string $note,
                                string $timeCreated,
                                string $timeUpdated,
                                string $transactionTier)
    {
        $this->guid = $guid;
        $this->providerUserGuid = $providerUserGuid;
        $this->currentPlace = $currentPlace;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
        $this->emailAddress = $emailAddress;
        $this->about = $about;
        $this->note = $note;
        $this->timeCreated = $timeCreated;
        $this->timeUpdated = $timeUpdated;
        $this->transactionTier = $transactionTier;
    }

    /**
     * @return mixed
     *
     * TODO: return $this->guid when interface is fixed
     */
    public function getGuid()
    {
        return new Guid($this->guid);
    }

    /**
     * @return string
     */
    public function getProviderUserGuid(): string
    {
        return $this->providerUserGuid;
    }

    /**
     * @param string $userGuid
     * @return bool
     */
    public function isProvider(string $userGuid)
    {
        return ($this->providerUserGuid === $userGuid);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    /**
     * @return string
     */
    public function getRefereeName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @param Address $address
     * @return FeedItem
     */
    public function setAddress(Address $address): FeedItem
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCityState()
    {
        return $this->getAddress()->getCity() . ', ' . $this->getAddress()->getState();
    }

    /**
     * @return string
     */
    public function getAbout(): string
    {
        return $this->about;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param RecipientInterface $recipient
     * @return FeedItem
     */
    public function setRecipient(RecipientInterface $recipient): FeedItem
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return RecipientInterface
     */
    public function getRecipient(): RecipientInterface
    {
        return $this->recipient;
    }

    /**
     * @return mixed
     */
    public function getAgreementName()
    {
        return $this->agreementName;
    }

    /**
     * @param string $agreementName
     * @return FeedItem
     */
    public function setAgreementName(string $agreementName): FeedItem
    {
        $this->agreementName = $agreementName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAgreementBid()
    {
        return $this->agreementBid;
    }

    /**
     * @param float $agreementBid
     * @return FeedItem
     */
    public function setAgreementBid(float $agreementBid): FeedItem
    {
        $this->agreementBid = $agreementBid;
        return $this;
    }

    /**
     * @param $rating
     * @return $this
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->getRating() == null ? null : $this->getRating()->getScore();
    }

    /**
     * @return mixed
     */
    public function getRatingNote()
    {
        return $this->getRating() == null ? null : $this->getRating()->getRatingNote();
    }

    /**
     * @return mixed
     */
    public function getResolution()
    {
        return $this->getRating() == null ? null : $this->getRating()->getResolution();
    }

    /**
     * @return mixed
     */
    public function getReviewerNote()
    {
        return $this->getRating() == null ? null : $this->getRating()->getReviewerNote();
    }

    /**
     * @return string
     */
    public function getTimeCreated(): string
    {
        return $this->timeCreated;
    }


    public function getTimeUpdated(): string
    {
        return $this->timeUpdated;
    }

    /**
     * @return mixed
     */
    public function getTransactionTier()
    {
        return $this->transactionTier;
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return $this->feedType;
    }

    public function getCardBody(): string
    {
        return '';
    }
}