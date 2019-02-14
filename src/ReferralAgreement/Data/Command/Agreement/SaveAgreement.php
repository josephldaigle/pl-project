<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 3:56 PM
 */


namespace PapaLocal\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;


/**
 * Class SaveAgreement
 *
 * @package PapaLocal\ReferralAgreement\Data\Command\Agreement
 */
class SaveAgreement
{
    /**
     * @var ReferralAgreement
     */
    private $referralAgreement;

    /**
     * SaveAgreement constructor.
     *
     * @param ReferralAgreement $referralAgreement
     */
    public function __construct(ReferralAgreement $referralAgreement)
    {
        $this->referralAgreement = $referralAgreement;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->referralAgreement->getGuid()->value();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->referralAgreement->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->referralAgreement->getDescription();
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->referralAgreement->getQuantity();
    }

    /**
     * @return string
     */
    public function getStrategy(): string
    {
        return $this->referralAgreement->getStrategy();
    }

    /**
     * @return float
     */
    public function getBid(): float
    {
        return $this->referralAgreement->getBid();
    }

    /**
     * @return string
     */
    public function getCompanyGuid(): string
    {
        return $this->referralAgreement->getCompanyGuid()->value();
    }

    /**
     * @return string
     */
    public function getOwnerGuid(): string
    {
        return (is_null($this->referralAgreement->getOwnerGuid())) ? '' : $this->referralAgreement->getOwnerGuid()->value();
    }

    /**
     * @return Collection
     */
    public function getIncludedLocations(): Collection
    {
        return $this->referralAgreement->getIncludedLocations();
    }

    /**
     * @return Collection
     */
    public function getExcludedLocations(): Collection
    {
        return $this->referralAgreement->getExcludedLocations();
    }

    /**
     * @return Collection
     */
    public function getIncludedServices(): Collection
    {
        return $this->referralAgreement->getIncludedServices();
    }

    /**
     * @return Collection
     */
    public function getExcludedServices(): Collection
    {
        return $this->referralAgreement->getExcludedServices();
    }
}