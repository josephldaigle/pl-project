<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/23/18
 * Time: 3:53 PM
 */

namespace PapaLocal\Referral\Data\Command;


use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\ValueObject\ReferralRating;


/**
 * Class UpdateReferral
 * @package PapaLocal\Referral\Data\Command
 */
class UpdateReferral
{
    /**
     * @var Referral
     */
    private $referral;

    /**
     * @var string
     */
    private $currentPlace;

    /**
     * UpdateReferral constructor.
     * @param Referral $referral
     * @param string $currentPlace
     */
    public function __construct(Referral $referral, string $currentPlace)
    {
        $this->referral = $referral;
        $this->currentPlace = $currentPlace;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->referral->getId();
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->referral->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getProviderUserGuid(): string
    {
        return $this->referral->getProviderUserGuid()->value();
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
    public function getFirstName(): string
    {
        return $this->referral->getFirstName();
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->referral->getLastName();
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->referral->getPhoneNumber()->getPhoneNumber();
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->referral->getEmailAddress()->getEmailAddress();
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        $array = array(
            'streetAddress' => $this->referral->getAddress()->getStreetAddress(),
            'city' => $this->referral->getAddress()->getCity(),
            'state' => $this->referral->getAddress()->getState(),
            'stateAbbreviated' => $this->referral->getAddress()->getStateAbbreviated(),
            'postalCode' => $this->referral->getAddress()->getPostalCode(),
            'country' => $this->referral->getAddress()->getCountry(),
            'countryAbbreviated' => $this->referral->getAddress()->getCountryAbbreviated(),
            'type' => $this->referral->getAddress()->getType(),

        );

        $json = json_encode($array);

        return $json;
    }

    /**
     * @return string
     */
    public function getAbout(): string
    {
        return $this->referral->getAbout();
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->referral->getNote();
    }

    /**
     * @return string
     */
    public function getAgreementGuid(): string
    {
        if ($this->referral->getRecipient() instanceof AgreementRecipient) {
            return $this->referral->getRecipient()->getGuid()->value();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getContactGuid(): string
    {
        if ($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getContactGuid()->value();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientFirstName(): string
    {
        if ($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getFirstName();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientLastName(): string
    {
        if ($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getLastName();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientPhoneNumber(): string
    {
        if ($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getPhoneNumber()->getPhoneNumber();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientEmailAddress(): string
    {
        if ($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getEmailAddress()->getEmailAddress();
        }
        return '';
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            return $this->referral->getRating()->getScore();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRatingNote(): string
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            return $this->referral->getRating()->getRatingNote();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getReviewerNote(): string
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            return $this->referral->getRating()->getReviewerNote();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getReviewerGuid(): string
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            return $this->referral->getRating()->getReviewerGuid()->value();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getResolution(): string
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            return $this->referral->getRating()->getResolution();
        }
        return '';
    }

    /**
     * @return bool
     */
    public function isContactRecipient(): bool
    {
        return ($this->referral->getRecipient() instanceof ContactRecipient);
    }

    /**
     * @return bool
     */
    public function isRated()
    {
        if ($this->referral->getRating() instanceof ReferralRating) {
            if ($this->referral->getRating()->getScore() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isDisputed()
    {
        if ($this->referral->getCurrentPlace() == 'disputed'){
            return true;
        }
        return false;
    }
}