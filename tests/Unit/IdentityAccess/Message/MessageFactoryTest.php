<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:19 PM
 */

namespace Test\Unit\IdentityAccess\Message;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Form\CreateUserAccountForm;
use PapaLocal\IdentityAccess\Message\Command\Company\UpdateCompanyName;
use PapaLocal\IdentityAccess\Message\Command\User\CreateUserAccount;
use PapaLocal\IdentityAccess\Message\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsername;
use PHPUnit\Framework\TestCase;


/**
 * Class MessageFactoryTest
 *
 * @package Test\Unit\IdentityAccess\Message
 */
class MessageFactoryTest extends TestCase
{
    /**
     * @var MessageFactory
     */
    private $iaMessageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // set up fixtures
        $this->iaMessageFactory = new MessageFactory();
    }

    public function testCanCreateNewCreateUserAccount()
    {
        // set up fixtures
        $formMock = $this->createMock(CreateUserAccountForm::class);

        // exercise SUT
        $result =  $this->iaMessageFactory->newCreateUserAccount($formMock);

        // make assertions
        $this->assertInstanceOf(CreateUserAccount::class, $result);
    }

    public function testCanCreateNewUpdatePassword()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $password = '$2y$13$OzFLSxqUg8do8VnCpPyOvuQcXXf6qNfWZCZYbm4Zv/fOtIGQVDeLW';

        // exercise SUT
        $result = $this->iaMessageFactory->newUpdatePassword($guidMock, $password);
        
        // make assertions
        $this->assertInstanceOf(UpdatePassword::class, $result);
    }

    public function testCanCreateNewFindUserByGuid()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $result = $this->iaMessageFactory->newFindUserByGuid($guidMock);

        // make assertions
        $this->assertInstanceOf(FindUserByGuid::class, $result);
    }

    public function testCanCreateFindUserByUsername()
    {
        // set up fixtures
        $emailAddressMock = 'test@papalocal.com';

        // exercise SUT
        $result = $this->iaMessageFactory->newFindUserByUsername($emailAddressMock);

        // make assertions
        $this->assertInstanceOf(FindUserByUsername::class, $result);
    }

    public function testCanCreateUpdateCompanyName()
    {
        // set up fixtures
        $guid = 'a6fc1282-cea5-45b9-ab6c-32d7848f5312';
        $name = 'Test New Name, LLC.';

        // exercise SUT
        $result = $this->iaMessageFactory->newUpdateCompanyName($guid, $name);

        // make assertions
        $this->assertInstanceOf(UpdateCompanyName::class, $result);
    }
}