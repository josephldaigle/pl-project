<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/17/18
 * Time: 8:28 AM
 */


namespace PapaLocal\IdentityAccess\Data;


use PapaLocal\Core\Data\AbstractHydrator;
use PapaLocal\Entity\Address;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\EmailAddress;
use PapaLocal\Entity\Entity;
use PapaLocal\Entity\PhoneNumber;
use PapaLocal\Entity\User;
use PapaLocal\ValueObject\ContactProfile;


/**
 * Class UserContactDetailHydrator
 *
 * @package PapaLocal\IdentityAccess\Data
 */
class UserContactDetailHydrator extends AbstractHydrator
{
    /**
     * @var ContactProfile
     */
    private $contactProfile;

    /**
     * @param Entity $entity
     *
     * @inheritDoc
     */
    public function setEntity(Entity $entity)
    {
        if (! $entity instanceof User ) {
            throw new \InvalidArgumentException(sprintf('%s expects param 1 to be an instance of %s. %s given.', __METHOD__, User::class, get_class($entity), __CLASS__));
        }

        if (! is_numeric($entity->getId())) {
            throw new \InvalidArgumentException(sprintf('Param 1 supplied to %s must have a getId() function that returns an integer.', __METHOD__));
        }
        $this->entity = $entity;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(): Entity
    {
        // instantiate a contact profile obj
        $collection = $this->serializer->denormalize(array(), Collection::class, 'array');
        $this->contactProfile = $this->serializer->denormalize(array(
                'emailList' => $collection,
                'phoneNumberList' => $collection,
                'addressList' => $collection),
            ContactProfile::class, 'array');

        // hydrate the profile obj
        $this->hydrateAddressList();
        $this->hydrateEmailAddressList();
        $this->hydratePhoneNumberList();

        // set profile on entity and return entity
        return $this->entity->setContactProfile($this->contactProfile);
    }

    /**
     * Hydrate the address list portion of the contact profile.
     */
    private function hydrateAddressList()
    {
        $this->tableGateway->setTable('v_user_address');
        $addressRows = $this->tableGateway->findBy('userId', $this->entity->getId());

        foreach ($addressRows as $row) {
            $address = $this->serializer->denormalize($row, Address::class, 'array');
            $this->contactProfile->addAddress($address);
        }
    }

    /**
     * Hydrate the email address list portion of the contact profile.
     */
    private function hydrateEmailAddressList()
    {
        $this->tableGateway->setTable('v_user_email_address');
        $emailRows = $this->tableGateway->findBy('userId', $this->entity->getId());

        foreach ($emailRows as $row) {
            $emailAddress = $this->serializer->denormalize($row, EmailAddress::class, 'array');
            $this->contactProfile->addEmailAddress($emailAddress);
        }
    }

    /**
     * Hydrate the phone number list portion of the contact profile.
     */
    private function hydratePhoneNumberList()
    {
        $this->tableGateway->setTable('v_user_phone');
        $phoneRows = $this->tableGateway->findBy('userId', $this->entity->getId());

        foreach ($phoneRows as $row) {
            $phoneNumber = $this->serializer->denormalize($row, PhoneNumber::class, 'array');
            $this->contactProfile->addPhoneNumber($phoneNumber);
        }
    }
}