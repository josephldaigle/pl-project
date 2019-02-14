<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/8/18
 * Time: 11:47 AM
 */

namespace PapaLocal\Data\Hydrate\Company;


use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\ValueObject\ContactProfile;

/**
 * CompanyContactProfileHydrator.
 *
 * @package PapaLocal\Data\Hydrate\Company
 */
class CompanyContactProfileHydrator extends AbstractHydrator
{
    /**
     * @var ContactProfile
     */
    private $contactProfile;

    /**
     * @param Entity $entity an instance of Company
     * @inheritDoc
     */
    public function setEntity(Entity $entity)
    {
        if (! $entity instanceof Company) {
            throw new \InvalidArgumentException(sprintf('Param 1 provided to %s must be an instance of %s',
                __METHOD__, Company::class));
        }

        $this->entity = $entity;
    }

    /**
     * Hydrate the entire contact profile.
     * 
     * @inheritDoc
     */
    public function hydrate(): Entity
    {
        // hydrate full contact profile
        $this->initContactProfile();
        $this->hydrateAddressList();
        $this->hydrateEmailAddressList();
        $this->hydratePhoneNumberList();

        return $this->entity;
    }

    /**
     * Hydrate the address list portion of the contact profile.
     */
    public function hydrateAddressList()
    {
        // initialize the contact profile
        if (is_null($this->contactProfile)) { $this->initContactProfile(); }

        $this->tableGateway->setTable('v_company_address');
        $addressRows = $this->tableGateway->findBy('companyId', $this->entity->getId());

        foreach($addressRows as $row) {
            $address = $this->serializer->denormalize($row, Address::class, 'array');
            $this->contactProfile->addAddress($address);
        }

        $this->entity->setContactProfile($this->contactProfile);
        return $this->entity;
    }

    /**
     * Hydrate the email address list portion of the contact profile.
     */
    public function hydrateEmailAddressList()
    {
        // initialize the contact profile
        if (is_null($this->contactProfile)) { $this->initContactProfile(); }

        $this->tableGateway->setTable('v_company_email');
        $emailRows = $this->tableGateway->findBy('companyId', $this->entity->getId());

        foreach ($emailRows as $row) {
            $emailAddress = $this->serializer->denormalize($row, EmailAddress::class, 'array');
            $this->contactProfile->addEmailAddress($emailAddress);
        }

        $this->entity->setContactProfile($this->contactProfile);
        return $this->entity;

    }

    /**
     * Hydrate the phone number list portion of the contact profile.
     */
    public function hydratePhoneNumberList()
    {
        // initialize the contact profile
        if (is_null($this->contactProfile)) { $this->initContactProfile(); }

        $this->tableGateway->setTable('v_company_phone');
        $phoneRows = $this->tableGateway->findBy('companyId', $this->entity->getId());

        foreach ($phoneRows as $row) {
            $phoneNumber = $this->serializer->denormalize($row, PhoneNumber::class, 'array');
            $this->contactProfile->addPhoneNumber($phoneNumber);
        }

        $this->entity->setContactProfile($this->contactProfile);
        return $this->entity;
    }

    /**
     * Initializes the ContactProfileObject
     */
    private function initContactProfile()
    {
        $collection = $this->serializer->denormalize(array(), Collection::class, 'array');

        $this->contactProfile = $this->serializer->denormalize(array(
                'emailList' => $collection,
                'addressList' => $collection,
                'phoneNumberList' => $collection
            ),
            ContactProfile::class, 'array');
    }

}