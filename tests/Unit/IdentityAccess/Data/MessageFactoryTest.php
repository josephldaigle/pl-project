<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 2:39 PM
 */

namespace Test\Unit\IdentityAccess\Data;


use PapaLocal\Core\ValueObject\Address;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Core\ValueObject\PhoneNumber;
use PapaLocal\Entity\Company;
use PapaLocal\IdentityAccess\Data\Command\Company\SaveCompany;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyAddress;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyEmail;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyName;
use PapaLocal\IdentityAccess\Data\Command\Company\UpdateCompanyPhone;
use PapaLocal\IdentityAccess\Data\Command\User\CreateUser;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateUserRoles;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PHPUnit\Framework\TestCase;


/**
 * Class MessageFactoryTest
 *
 * @package Test\Unit\IdentityAccess\Data
 */
class MessageFactoryTest extends TestCase
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->messageFactory = new MessageFactory();
    }

    public function testCanCreateNewCreateUser()
    {
        // set up fixtures
        $userMock = $this->createMock(User::class);

        // exercise SUT
        $result = $this->messageFactory->newCreateUser($userMock);

        // make assertions
        $this->assertInstanceOf(CreateUser::class, $result);
    }

    public function testCanCreateNewUpdateUserRoles()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $rolesArray = [];

        // exercise SUT
        $result = $this->messageFactory->newUpdateUserRoles($guidMock, $rolesArray);

        // make assertions
        $this->assertInstanceOf(UpdateUserRoles::class, $result);
    }

    public function testCanCreateNewUpdatePassword()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $password = '$2y$13$Zu4lkTBnSdVbdmy//ixlvueAHTK9k5EpEn5.Qz6qlwBQvCimMBor.';

        // exercise SUT
        $result = $this->messageFactory->newUpdatePassword($guidMock, $password);

        // make assertions
        $this->assertInstanceOf(UpdatePassword::class, $result);
    }

    public function testCanCreateNewSaveCompany()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $companyMock = $this->createMock(Company::class);

        // exercise SUT
        $result = $this->messageFactory->newSaveCompany($guidMock, $companyMock);

        // make assertions
        $this->assertInstanceOf(SaveCompany::class, $result);
    }

    public function testCanCreateNewUpdateCompanyName()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $name = 'New Test Name, LLC.';

        // exercise SUT
        $result = $this->messageFactory->newUpdateCompanyName($guidMock, $name);

        // make assertions
        $this->assertInstanceOf(UpdateCompanyName::class, $result);
    }

    public function testCanCreateNewUpdateCompanyPhoneNumber()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $phone = $this->createMock(PhoneNumber::class);

        // exercise SUT
        $result = $this->messageFactory->newUpdateCompanyPhoneNumber($guidMock, $phone);

        // make assertions
        $this->assertInstanceOf(UpdateCompanyPhone::class, $result);
    }

    public function testCanCreateNewUpdateCompanyEmailAddress()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $emailAddress = $this->createMock(EmailAddress::class);

        // exercise SUT
        $result = $this->messageFactory->newUpdateCompanyEmailAddress($guidMock, $emailAddress);

        // make assertions
        $this->assertInstanceOf(UpdateCompanyEmail::class, $result);
    }

    public function testCanCreateNewUpdateCompanyAddress()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $address = $this->createMock(Address::class);

        // exercise SUT
        $result = $this->messageFactory->newUpdateCompanyAddress($guidMock, $address);

        // make assertions
        $this->assertInstanceOf(UpdateCompanyAddress::class, $result);
    }
}