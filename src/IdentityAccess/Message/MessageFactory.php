<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 9:48 PM
 */


namespace PapaLocal\IdentityAccess\Message;


use PapaLocal\IdentityAccess\Form\Company\CreateCompany as CreateCompanyForm;
use PapaLocal\IdentityAccess\Message\Command\Company\CreateCompany;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Form\CreateUserAccountForm;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyAddress;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyEmailAddress;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyName;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyPhoneNumber;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyWebsite;
use PapaLocal\IdentityAccess\Message\Command\SavePerson;
use PapaLocal\IdentityAccess\Message\Command\User\CreateUserAccount;
use PapaLocal\IdentityAccess\Message\Command\User\UpdateFirstName;
use PapaLocal\IdentityAccess\Message\Command\User\UpdateLastName;
use PapaLocal\IdentityAccess\Message\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Message\Command\User\UpdatePhoneNumber;
use PapaLocal\IdentityAccess\Message\Command\User\UpdateUserAddress;
use PapaLocal\IdentityAccess\Message\Query\Company\FindByUserGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsername;


/**
 * Class MessageFactory
 *
 * @package PapaLocal\IdentityAccess\Message
 */
class MessageFactory
{
    /**
     * @param CreateUserAccountForm $createUserAccountForm
     *
     * @return CreateUserAccount
     */
    public function newCreateUserAccount(CreateUserAccountForm $createUserAccountForm): CreateUserAccount
    {
        return new CreateUserAccount($createUserAccountForm);
    }

    /**
     * TODO: Refactor
     * @param GuidInterface $personId
     * @param string        $firstName
     * @param string        $lastName
     * @param string        $about
     * @param array         $phoneList
     * @param array         $emailList
     * @param array         $addressList
     *
     * @return SavePerson
     */
    public function newSavePerson(GuidInterface $personId, string $firstName, string $lastName, string $about = '', array $phoneList = [], array $emailList = [], array $addressList = []): SavePerson
    {
        return new SavePerson($personId, $firstName, $lastName, $about, $phoneList, $emailList, $addressList);
    }

    /**
     * @param string $userGuid
     * @param string $firstName
     *
     * @return UpdateFirstName
     */
    public function newUpdateFirstName(string $userGuid, string $firstName): UpdateFirstName
    {
        return new UpdateFirstName($userGuid, $firstName);
    }

    /**
     * @param string $userGuid
     * @param string $lastName
     *
     * @return UpdateLastName
     */
    public function newUpdateLastName(string $userGuid, string $lastName): UpdateLastName
    {
        return new UpdateLastName($userGuid, $lastName);
    }

    /**
     * @param string $userGuid
     * @param string $phoneNumber
     * @param string $phoneType
     *
     * @return UpdatePhoneNumber
     */
    public function newUpdateUserPhoneNumber(string $userGuid, string $phoneNumber, string $phoneType): UpdatePhoneNumber
    {
        return new UpdatePhoneNumber($userGuid, $phoneNumber, $phoneType);
    }

    /**
     * @param string $userGuid
     * @param string $streetAddress
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $country
     * @param string $type
     *
     * @return UpdateUserAddress
     */
    public function newUpdateUserAddress(string $userGuid, string $streetAddress, string $city, string $state, string $postalCode, string $country, string $type): UpdateUserAddress
    {
        return new UpdateUserAddress($userGuid, $streetAddress, $city, $state, $postalCode, $country, $type);
    }

    /**
     * @param GuidInterface     $ownerUserGuid
     * @param CreateCompanyForm $form
     *
     * @return CreateCompany
     */
    public function newCreateCompany(GuidInterface $ownerUserGuid, CreateCompanyForm $form): CreateCompany
    {
        return new CreateCompany($ownerUserGuid, $form);
    }

    /**
     * @param string        $companyGuid
     * @param string        $name
     *
     * @return UpdateCompanyName
     */
    public function newUpdateCompanyName(string $companyGuid, string $name): UpdateCompanyName
    {
        return new UpdateCompanyName($companyGuid, $name);
    }

    /**
     * @param string $companyGuid
     * @param string $phoneNumber
     * @param string $phoneType
     *
     * @return UpdateCompanyPhoneNumber
     */
    public function newUpdateCompanyPhoneNumber(string $companyGuid, string $phoneNumber, string $phoneType): UpdateCompanyPhoneNumber
    {
        return new UpdateCompanyPhoneNumber($companyGuid, $phoneNumber, $phoneType);
    }

    /**
     * @param string $companyGuid
     * @param string $emailAddress
     * @param string $emailType
     *
     * @return UpdateCompanyEmailAddress
     */
    public function newUpdateCompanyEmailAddress(string $companyGuid, string $emailAddress, string $emailType): UpdateCompanyEmailAddress
    {
        return new UpdateCompanyEmailAddress($companyGuid, $emailAddress, $emailType);
    }

    /**
     * @param string $companyGuid
     * @param string $streetAddress
     * @param string $city
     * @param string $state
     * @param string $postalCode
     * @param string $country
     * @param string $type
     *
     * @return UpdateCompanyAddress
     */
    public function newUpdateCompanyAddress(string $companyGuid, string $streetAddress, string $city, string $state, string $postalCode, string $country, string $type): UpdateCompanyAddress
    {
        return new UpdateCompanyAddress($companyGuid, $streetAddress, $city, $state, $postalCode, $country, $type);
    }

    /**
     * @param string $companyGuid
     * @param string $website
     *
     * @return UpdateCompanyWebsite
     */
    public function newUpdateCompanyWebsite(string $companyGuid, string $website): UpdateCompanyWebsite
    {
        return new UpdateCompanyWebsite($companyGuid, $website);
    }

    /**
     * @param GuidInterface $guid
     * @param string        $password
     *
     * @return UpdatePassword
     */
    public function newUpdatePassword(GuidInterface $guid, string $password): UpdatePassword
    {
        return new UpdatePassword($guid, $password);
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return FindByUserGuid
     */
    public function newFindCompanyByUserGuid(GuidInterface $userGuid): FindByUserGuid
    {
        return new FindByUserGuid($userGuid);
    }

    /**
     * @param GuidInterface $userGuid
     *
     * @return FindUserByGuid
     */
    public function newFindUserByGuid(GuidInterface $userGuid): FindUserByGuid
    {
        return new FindUserByGuid($userGuid);
    }

    /**
     * @param string $username
     *
     * @return FindUserByUsername
     */
    public function newFindUserByUsername(string $username): FindUserByUsername
    {
        return new FindUserByUsername($username);
    }
}