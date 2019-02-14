<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 12:12 PM
 */

namespace Test\Unit\IdentityAccess\Service;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Entity\UserAccount;
use PapaLocal\IdentityAccess\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;


/**
 * Class UserServiceTest
 *
 * @package Test\Unit\IdentityAccess\Service
 */
class UserServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $workflowRegistryMock,
        $userRepositoryMock,
        $mysqlMsgFacMock,
        $mysqlBusMock,
        $passwordEncoderMock,
        $workflowMock;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->workflowRegistryMock = $this->createMock(Registry::class);
        $this->userRepositoryMock   = $this->createMock(UserRepository::class);
        $this->mysqlMsgFacMock = $this->createMock(MessageFactory::class);
        $this->mysqlBusMock = $this->createMock(MessageBusInterface::class);
        $this->passwordEncoderMock  = $this->createMock(UserPasswordEncoderInterface::class);
        $this->workflowMock = $this->createMock(Workflow::class);

        $this->userService = new UserService($this->workflowRegistryMock, $this->userRepositoryMock, $this->mysqlMsgFacMock, $this->mysqlBusMock, $this->passwordEncoderMock);
    }

    public function testCanCreateUserAccount()
    {
        // set up fixtures
        $password = 'SomeP@s$w0rd1!';
        $encodedPassword = '$2y$13$4t1Ib11OloLwHuMIY.cLFuctx7ixcJmt.jHX4ekVHeT95174NEKAq';

        $this->passwordEncoderMock->expects($this->once())
            ->method('encodePassword')
            ->with($this->equalTo(new User()), $this->equalTo($password))
            ->willReturn($encodedPassword);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('getPassword')
            ->willReturn($password);
        $userMock->expects($this->once())
            ->method('setPassword')
            ->with($this->equalTo($encodedPassword))
            ->willReturn($userMock);

        $userAcctMock = $this->createMock(UserAccount::class);
        $userAcctMock->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($userMock);
        $userAcctMock->expects($this->once())
            ->method('setUser')
            ->with($this->equalTo($userMock))
            ->willReturn($userAcctMock);

        $this->workflowRegistryMock->expects($this->once())
                                   ->method('get')
                                   ->with($this->equalTo($userAcctMock), $this->equalTo('user_account'))
                                   ->willReturn($this->workflowMock);
        
        $this->workflowMock->expects($this->once())
            ->method('apply')
            ->with($this->equalTo($userAcctMock), 'create');

        // exercise SUT
        $this->userService->createUserAccount($userAcctMock);
    }

    public function testCanUpdateUserPassword()
    {
        // set up fixtures
        $unencodedPass = 'SomeP@SSW0rd123!@#';
        $encodedPass = '$2y$13$P3TcvkF2iTY79TnCOvahK.AZ2zHHv70aiv6.J6bDsRUdX35mjWTky';

        $guidMock = $this->createMock(GuidInterface::class);

        $commandMock = $this->createMock(UpdatePassword::class);

        $this->passwordEncoderMock->expects($this->once())
            ->method('encodePassword')
            ->with($this->equalTo(new User()), $this->equalTo($unencodedPass))
            ->willReturn($encodedPass);

        $this->mysqlMsgFacMock->expects($this->once())
            ->method('newUpdatePassword')
            ->with($this->equalTo($guidMock), $this->equalTo($encodedPass))
            ->willReturn($commandMock);

        $this->mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($commandMock));

        // exercise SUT
        $this->userService->updatePassword($guidMock, $unencodedPass);
    }
}