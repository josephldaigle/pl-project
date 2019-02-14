<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 4:36 PM
 */

namespace PapaLocal\ReferralAgreement\Entity\Factory;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;


/**
 * Class ReferralAgreementBuilder
 *
 * @package PapaLocal\ReferralAgreement\Entity\Factory
 */
class ReferralAgreementBuilder
{
    /**
     * @var array holds the values used to create the agreement.
     */
    private $data = [];

    /**
     * Each setter that sets a required field decrements the number, which will eventually be 0 (false), when all required setters have been called.
     *
     * This var is reset each time the build() function is invoked, or the __constructor of this class is used.
     *
     * @var int the number of required fields.
     */
    private $records;

    /**
     * @var array holds a permanent list of required setter calls.
     */
    private $methods = [
        'setGuid',
        'setCompanyGuid',
        'setName',
        'setDescription',
        'setQuantity',
        'setStrategy',
        'setBid'
    ];

    /**
     * ReferralAgreementBuilder constructor.
     */
    public function __construct()
    {
        $this->requiredCalls = $this->methods;
    }

    /**
     * Build the referral agreement.
     *
     * @return ReferralAgreement
     * @throws \BadMethodCallException
     */
    public function build(): ReferralAgreement
    {
        if (count($this->requiredCalls) > 0) {

            throw new \BadMethodCallException(sprintf('All required setters have not been called, and some data needed to create the agreement is missing. Fields: %s.', (implode(", ", $this->requiredCalls))
            ));
        }

        // return referral agreement
        $referralAgreement = new ReferralAgreement(
            $this->data['agreementGuid'],
            $this->data['companyGuid'],
            $this->data['name'],
            $this->data['description'],
            $this->data['quantity'],
            $this->data['strategy'],
            $this->data['bid']
        );

        if (isset($this->data['includedLocations'])) {
            $referralAgreement->setIncludedLocations($this->data['includedLocations']);
        }

        if (isset($this->data['excludedLocations'])) {
            $referralAgreement->setExcludedLocations($this->data['excludedLocations']);
        }

        if (isset($this->data['includedServices'])) {
            $referralAgreement->setIncludedLocations($this->data['includedLocations']);
        }

        if (isset($this->data['excludedServices'])) {
            $referralAgreement->setExcludedLocations($this->data['excludedLocations']);
        }

        if (isset($this->data['ownerGuid'])) {
            $referralAgreement->setOwnerGuid($this->data['ownerGuid']);
        }

        if (isset($this->data['statusHistory'])) {
            $referralAgreement->setStatusHistory($this->data['statusHistory']);
        }

        if (isset($this->data['invitees'])) {
            $referralAgreement->setInvitees($this->data['invitees']);
        }

        // reset the builder
        $this->requiredCalls = $this->methods;
        $this->data = [];

        // return agreement
        return $referralAgreement;

    }

    public function setGuid(Guid $agreementGuid): ReferralAgreementBuilder
    {
        $this->data['agreementGuid'] = $agreementGuid;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setCompanyGuid(Guid $companyGuid): ReferralAgreementBuilder
    {
        $this->data['companyGuid'] = $companyGuid;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }
        
        return $this;
    }

    public function setName(string $name): ReferralAgreementBuilder
    {
        $this->data['name'] = $name;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setDescription(string $description): ReferralAgreementBuilder
    {
        $this->data['description'] = $description;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setQuantity(int $quantity): ReferralAgreementBuilder
    {
        $this->data['quantity'] = $quantity;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setStrategy(string $strategy): ReferralAgreementBuilder
    {
        $this->data['strategy'] = $strategy;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setBid(float $bid): ReferralAgreementBuilder
    {
        $this->data['bid'] = $bid;

        if (($key = array_search(__FUNCTION__, $this->requiredCalls)) !== false) {
            unset($this->requiredCalls[$key]);
        }

        return $this;
    }

    public function setOwner(Guid $ownerGuid): ReferralAgreementBuilder
    {
        $this->data['ownerGuid'] = $ownerGuid;

        return $this;
    }

    public function setIncludedLocations(Collection $includedLocations): ReferralAgreementBuilder
    {
        $this->data['includedLocations'] = $includedLocations;

        return $this;
    }

    public function setExcludedLocations(Collection $excludedLocations): ReferralAgreementBuilder
    {
        $this->data['excludedLocations'] = $excludedLocations;

        return $this;
    }

    public function setIncludedServices(Collection $includedServices): ReferralAgreementBuilder
    {
        $this->data['includedServices'] = $includedServices;

        return $this;
    }

    public function setExcludedServices(Collection $excludedServices): ReferralAgreementBuilder
    {
        $this->data['excludedServices'] = $excludedServices;

        return $this;
    }

    public function setInvitees(Collection $invitees): ReferralAgreementBuilder
    {
        $this->data['invitees'] = $invitees;

        return $this;
    }
}