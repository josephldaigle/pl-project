<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/1/18
 */


namespace PapaLocal\IdentityAccess\Data;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\Data\AbstractMessageFactory;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Core\ValueObject\PhoneNumberType;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\Entity\Company;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\Command\Company\SaveCompany;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyAddress;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyEmail;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyName;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyPhone;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyWebsite;
use PapaLocal\IdentityAccess\Data\Command\UpdatePersonPhoneList;
use PapaLocal\IdentityAccess\Data\Command\User\CreateUser;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateAddress;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateFirstName;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateLastName;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePhoneNumber;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateUsername;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateUserRoles;
use PapaLocal\IdentityAccess\Data\Query\Company\FindByUserGuid;
use PapaLocal\IdentityAccess\Message\Command\SavePerson;


/**
 * Class MessageFactory.
 *
 * @package PapaLocal\IdentityAccess\Data
 */
class MessageFactory extends AbstractMessageFactory
{
    /**
     * User Commands
     */


    /**
     * @param User $user
     *
     * @return CreateUser
     */
    public function newCreateUser(User $user): CreateUser
    {
        return new CreateUser($user);
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $username
     *
     * @return UpdateUsername
     */
    public function newUpdateUsername(GuidInterface $userGuid, string $username): UpdateUsername
    {
        return new UpdateUsername($userGuid, $username);
    }

    /**
     * @param GuidInterface $userGuid
     * @param array         $roles
     *
     * @return UpdateUserRoles
     */
    public function newUpdateUserRoles(GuidInterface $userGuid, array $roles): UpdateUserRoles
    {
        return new UpdateUserRoles($userGuid, $roles);
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $password
     *
     * @return UpdatePassword
     */
    public function newUpdatePassword(GuidInterface $userGuid, string $password): UpdatePassword
    {
        return new UpdatePassword($userGuid, $password);
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $phoneNumber
     *
     * @return UpdatePhoneNumber
     */
    public function newUpdateUserPhoneNumber(GuidInterface $userGuid, string $phoneNumber, PhoneNumberType $phoneNumberType): UpdatePhoneNumber
    {
        return new UpdatePhoneNumber($userGuid, $phoneNumber, $phoneNumberType);
    }

    /**
     * @param GuidInterface $userGuid
     * @param Address       $address
     *
     * @return UpdateAddress
     */
    public function newUpdateUserAddress(GuidInterface $userGuid, Address $address): UpdateAddress
    {
        return new UpdateAddress($userGuid, $address);
    }

    /**
     * @param GuidInterface $personId
     * @param string        $firstName
     * @param string        $lastName
     * @param string        $about
     *
     * @return SavePerson
     */
    public function newSavePerson(
        GuidInterface $personId,
        string $firstName,
        string $lastName,
        string $about = ''
    ): SavePerson
    {
        return new SavePerson($personId, $firstName, $lastName, $about);
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $firstName
     *
     * @return UpdateFirstName
     */
    public function newUpdateFirstName(GuidInterface $userGuid, string $firstName): UpdateFirstName
    {
        return new UpdateFirstName($userGuid, $firstName);
    }

    /**
     * @param GuidInterface $userGuid
     * @param string        $lastName
     *
     * @return UpdateLastName
     */
    public function newUpdateLastName(GuidInterface $userGuid, string $lastName): UpdateLastName
    {
        return new UpdateLastName($userGuid, $lastName);
    }

    public function newUpdatePersonPhoneList(GuidInterface $personId, Collection $phoneNumbers)
    {
         return new UpdatePersonPhoneList($personId, $phoneNumbers);
    }

    public function newUpdatePersonAddressList()
    {
        // TODO: Implement
    }

    public function newUpdatePersonEmailAddressList()
    {
        // TODO: Implement
    }

    /**
     * Company Commands
     */

    /**
     * @param GuidInterface $ownerUserGuid
     * @param Company       $company
     *
     * @return SaveCompany
     */
    public function newSaveCompany(GuidInterface $ownerUserGuid, Company $company): SaveCompany
    {
        return new SaveCompany($ownerUserGuid, $company);
    }

    /**
     * @param GuidInterface $companyGuid
     * @param string        $name
     *
     * @return UpdateCompanyName
     */
    public function newUpdateCompanyName(GuidInterface $companyGuid, string $name): UpdateCompanyName
    {
        return new UpdateCompanyName($companyGuid, $name);
    }

    /**
     * @param GuidInterface $companyGuid
     * @param PhoneNumber   $phoneNumber
     *
     * @return UpdateCompanyPhone
     */
    public function newUpdateCompanyPhoneNumber(GuidInterface $companyGuid, PhoneNumber $phoneNumber): UpdateCompanyPhone
    {
        return new UpdateCompanyPhone($companyGuid, $phoneNumber);
    }

    /**
     * @param GuidInterface $companyGuid
     * @param EmailAddress  $emailAddress
     *
     * @return UpdateCompanyEmail
     */
    public function newUpdateCompanyEmailAddress(GuidInterface $companyGuid, EmailAddress $emailAddress): UpdateCompanyEmail
    {
        return new UpdateCompanyEmail($companyGuid, $emailAddress);
    }

    /**
     * @param GuidInterface $companyGuid
     * @param Address       $address
     *
     * @return UpdateCompanyAddress
     */
    public function newUpdateCompanyAddress(GuidInterface $companyGuid, Address $address): UpdateCompanyAddress
    {
        return new UpdateCompanyAddress($companyGuid, $address);
    }

    /**
     * @param GuidInterface $companyGuid
     * @param string        $website
     *
     * @return UpdateCompanyWebsite
     */
    public function newUpdateCompanyWebsite(GuidInterface $companyGuid, string $website): UpdateCompanyWebsite
    {
        return new UpdateCompanyWebsite($companyGuid, $website);
    }

    /**
     * @param string $userGuid
     *
     * @return FindByUserGuid
     */
    public function newFindCompanyByUserGuid(string $userGuid): FindByUserGuid
    {
        return new FindByUserGuid($userGuid);
    }

}