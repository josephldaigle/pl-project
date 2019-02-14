<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/17/18
 * Time: 2:58 PM
 */

namespace PapaLocal\Referral\Data\Command;


use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;


/**
 * Class SaveReferral
 * @package PapaLocal\Referral\Data\Command
 */
class SaveReferral
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
     * SaveReferral constructor.
     * @param Referral $referral
     * @param string $currentPlace
     */
    public function __construct(Referral $referral, string $currentPlace)
    {
        $this->referral = $referral;
        $this->currentPlace = $currentPlace;
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
        return $this->referral->getAddress();
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
        if($this->referral->getRecipient() instanceof AgreementRecipient) {
            return $this->referral->getRecipient()->getGuid()->value();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientFirstName(): string
    {
        if($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getFirstName();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientLastName(): string
    {
        if($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getLastName();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientPhoneNumber(): string
    {
        if($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getPhoneNumber()->getPhoneNumber();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRecipientEmailAddress(): string
    {
        if($this->referral->getRecipient() instanceof ContactRecipient) {
            return $this->referral->getRecipient()->getEmailAddress()->getEmailAddress();
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
}