<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/16/18
 * Time: 1:15 PM
 */

namespace Test\Unit\Service;

use PapaLocal\AuthorizeDotNet\AuthorizeDotNet;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\Command\User\Billing\CreateBillingProfile;
use PapaLocal\Data\DataService;
use PapaLocal\Billing\Data\BillingProfileRepository;
use PapaLocal\Entity\Person;
use PapaLocal\Entity\User;
use PapaLocal\Service\BillingProfileManager;
use Doctrine\DBAL\DBALException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * BillingProfileManagerTest.
 *
 */
class BillingProfileManagerTest extends TestCase
{
    /**
     * @var AuthorizeDotNet
     */
    private $aNetApiMock;

    /**
     * @var LoggerInterface
     */
    private $loggerMock;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->billProRepo = $this->createMock(BillingProfileRepository::class);
        $this->aNetApiMock = $this->createMock(AuthorizeDotNet::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\BillingProfileOperationException
     * @expectedExceptionMessageRegExp /^(Unable to create billing profile for)/
     */
    public function testCreateBillingProfileThrowsExceptionWhenAuthorizeNetCannotCreateProfile()
    {
        $this->markTestIncomplete('not yet implemented');

        // set up fixtures
        $personMock = $this->createMock(Person::class);
        $personMock->expects($this->once())
            ->method('getFirstName')
            ->willReturn('Guy');
        $personMock->expects($this->once())
            ->method('getLastName')
            ->willReturn('Tester');

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->exactly(2))
            ->method('getPerson')
            ->willReturn($personMock);
        $userMock->expects($this->exactly(2))
            ->method('getUsername')
            ->willReturn('guy@tester.com');

        $persistenceMock = $this->createMock(DataService::class);
        $persistenceMock->expects($this->never())
            ->method('setCommand');
        $persistenceMock->expects($this->never())
            ->method('execute');

        $commandFacMock = $this->createMock(CommandFactory::class);
        $commandFacMock->expects($this->never())
            ->method('createCommand');

        $this->aNetApiMock->expects($this->once())
            ->method('createCustomerProfile')
            ->willReturn(false);

        // use reflection to expose protected SUT
        $reflection = new \ReflectionClass(BillingProfileManager::class);
        $method = $reflection->getMethod('createBillingProfile');
        $method->setAccessible(true);


        // exercise SUT
        $obj = $reflection->newInstanceArgs([$persistenceMock, $commandFacMock, $this->aNetApiMock, $this->loggerMock]);
        $method->invokeArgs($obj, [$userMock]);
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\BillingProfileOperationException
     * @expectedExceptionMessageRegExp /^(Unable to save billing profile for)(.)+(Customer id)/
     */
    public function testCreateBillingProfileThrowsExceptionWhenCannotSaveProfileToDatabase()
    {
        $this->markTestIncomplete('not yet implemented');

        // set up fixtures
        // mock a person
        $personMock = $this->createMock(Person::class);
        $personMock->expects($this->once())
            ->method('getFirstName')
            ->willReturn('Guy');
        $personMock->expects($this->once())
            ->method('getLastName')
            ->willReturn('Tester');

        // mock a user
        $userMock = $this->createMock(User::class);
        $userMock->expects($this->exactly(2))
            ->method('getPerson')
            ->willReturn($personMock);
        $userMock->expects($this->once())
            ->method('getId')
            ->willReturn(14);
        $userMock->expects($this->exactly(3))
            ->method('getUsername')
            ->willReturn('guy@tester.com');

        // config aNetApiMock
        $custId = 105678192;
        $this->aNetApiMock->expects($this->once())
            ->method('createCustomerProfile')
            ->willReturn($custId);

        // mock the query command
        $qryCmdMock = $this->createMock(CreateBillingProfile::class);

        // mock the command factory
        $commandFacMock = $this->createMock(CommandFactory::class);
        $commandFacMock->expects($this->once())
            ->method('createCommand')
            ->with(CreateBillingProfile::class, [14, $custId])
            ->willReturn($qryCmdMock);

        // mock the data service
        $persistenceMock = $this->createMock(DataService::class);
        $persistenceMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new DBALException());

        // use reflection to expose protected SUT
        $reflection = new \ReflectionClass(BillingProfileManager::class);
        $method = $reflection->getMethod('createBillingProfile');
        $method->setAccessible(true);

        // exercise SUT
        $obj = $reflection->newInstanceArgs([$persistenceMock, $commandFacMock, $this->aNetApiMock, $this->loggerMock]);
        $method->invokeArgs($obj, [$userMock]);
    }

    public function testCreateBillingProfileReturnsIdOnSuccess()
    {
        $this->markTestIncomplete('not yet implemented');
        // set up fixtures
        // mock a person
        $personMock = $this->createMock(Person::class);
        $personMock->expects($this->once())
            ->method('getFirstName')
            ->willReturn('Guy');
        $personMock->expects($this->once())
            ->method('getLastName')
            ->willReturn('Tester');

        // mock a user
        $userMock = $this->createMock(User::class);
        $userMock->expects($this->exactly(2))
            ->method('getPerson')
            ->willReturn($personMock);
        $userMock->expects($this->once())
            ->method('getId')
            ->willReturn(14);
        $userMock->expects($this->exactly(1))
            ->method('getUsername')
            ->willReturn('guy@tester.com');

        // config aNetApiMock
        $custId = 105678192;
        $this->aNetApiMock->expects($this->once())
            ->method('fetchCustomerProfile')
            ->willReturn($custId);

        // mock the query command
        $qryCmdMock = $this->createMock(CreateBillingProfile::class);

        // mock the command factory
        $commandFacMock = $this->createMock(CommandFactory::class);
        $commandFacMock->expects($this->once())
            ->method('createCommand')
            ->with(CreateBillingProfile::class, [14, $custId])
            ->willReturn($qryCmdMock);

        // mock the data service
        $persistenceMock = $this->createMock(DataService::class);
        $persistenceMock->expects($this->once())
            ->method('execute')
            ->with($qryCmdMock)
            ->willReturn(82);

        // use reflection to expose protected SUT
        $reflection = new \ReflectionClass(BillingProfileManager::class);
        $method = $reflection->getMethod('createBillingProfile');
        $method->setAccessible(true);

        // exercise SUT
        $obj = $reflection->newInstanceArgs([$persistenceMock, $commandFacMock, $this->aNetApiMock, $this->loggerMock]);
        $result = $method->invokeArgs($obj, [$userMock]);

        // make assertions
        $this->assertTrue(is_numeric($result), 'unexpected type');
        $this->assertEquals(82, $result, 'unexpected value');
    }
}