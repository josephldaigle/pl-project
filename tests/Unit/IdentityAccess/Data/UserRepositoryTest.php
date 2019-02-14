<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/21/18
 */

namespace Test\Unit\IdentityAccess\Data;


use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\MessageFactory;
use PapaLocal\IdentityAccess\Data\UserContactDetailHydrator;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Entity\Factory\UserFactory;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class UserRepositoryTest.
 *
 * @package Test\Unit\IdentityAccess\Data
 */
class UserRepositoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $dataPoolMock,
        $contactProfileHydMock,
        $mysqlBusMock,
        $mysqlFacMock,
        $userFactoryMock;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dataPoolMock          = $this->createMock(DataResourcePool::class);
        $this->contactProfileHydMock = $this->createMock(UserContactDetailHydrator::class);
        $this->mysqlBusMock          = $this->createMock(MessageBusInterface::class);
        $this->mysqlFacMock          = $this->createMock(MessageFactory::class);
        $this->userFactoryMock       = $this->createMock(UserFactory::class);

        $this->userRepository = new UserRepository($this->dataPoolMock, $this->contactProfileHydMock,
            $this->mysqlBusMock, $this->mysqlFacMock, $this->userFactoryMock);
    }


    /**
     * @expectedException PapaLocal\Entity\Exception\UserNotFoundException
     * @expectedExceptionMessageRegExp /(ec84a16e-1287-468e-a229-1aa97526c293)/
     */
    public function testFindUserByGuidThrowsExceptionWhenUserNotFound()
    {
        // set up fixtures
        $userGuid = 'ec84a16e-1287-468e-a229-1aa97526c293';
        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->exactly(2))
                 ->method('value')
                 ->willReturn($userGuid);
        $userMock = $this->createMock(User::class);

        $queryMock = $this->createMock(FindBy::class);

        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $recordSetMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->mysqlFacMock->expects($this->once())
                           ->method('newFindBy')
                           ->with($this->equalTo('v_user'), $this->equalTo('userGuid'), $this->equalTo($userGuid))
                           ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        // exercise SUT
        $this->userRepository->findUserByGuid($guidMock);
    }

    public function testFindUserByGuidIsSuccess()
    {
        // set up fixtures
        $userGuid = 'ec84a16e-1287-468e-a229-1aa97526c293';
        $userId = 1;
        $rolesMock = [
            SecurityRole::ROLE_USER()->getValue(),
            SecurityRole::ROLE_ADMIN()->getValue()
        ];

        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->once())
                 ->method('value')
                 ->willReturn($userGuid);

        $queryMock = $this->createMock(FindBy::class);

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('userId'))
            ->willReturn($userId);

        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $recordSetMock->expects($this->once())
            ->method('count')
            ->willReturn(1);
        $recordSetMock->expects($this->exactly(2))
            ->method('current')
            ->willReturn($recordMock);

        $userMock  = $this->createMock(User::class);
        $userMock->expects($this->once())
                 ->method('setRoles')
                 ->willReturn($userMock);

        $this->mysqlFacMock->expects($this->once())
                           ->method('newFindBy')
                           ->with($this->equalTo('v_user'), $this->equalTo('userGuid'), $this->equalTo($userGuid))
                           ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        $this->userFactoryMock->expects($this->once())
                              ->method('createFromRecord')
                              ->with($this->equalTo($recordMock))
                              ->willReturn($userMock);

        // exercise SUT
        $userRepo = $this->getMockBuilder(UserRepository::class)
            ->setConstructorArgs([$this->dataPoolMock, $this->contactProfileHydMock, $this->mysqlBusMock, $this->mysqlFacMock, $this->userFactoryMock])
            ->setMethodsExcept(['findUserByGuid'])
            ->getMock();

        $userRepo->expects($this->once())
            ->method('loadUserRoles')
            ->with($this->equalTo($userId))
            ->willReturn($rolesMock);

        $result = $userRepo->findUserByGuid($guidMock);

        // make assertion
        $this->assertEquals($userMock, $result);
    }

    /**
     * @expectedException PapaLocal\Entity\Exception\UserNotFoundException
     * @expectedExceptionMessageRegExp /(test@papalocal)/
     */
    public function testFindUserByUsernameThrowExceptionWhenUserNotFound()
    {
        // set up fixtures
        $username     = 'test@papalocal.com';
        $usernameMock = $this->createMock(EmailAddress::class);
        $usernameMock->expects($this->exactly(2))
            ->method('getEmailAddress')
            ->willReturn($username);

        $queryMock = $this->createMock(FindBy::class);

        $this->mysqlFacMock->expects($this->once())
                           ->method('newFindBy')
                           ->with($this->equalTo('v_user'), $this->equalTo('username'), $this->equalTo($username))
                           ->willReturn($queryMock);

        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $recordSetMock->expects($this->once())
                   ->method('count')
                   ->willReturn(0);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        // exercise SUT
        $this->userRepository->findUserByUsername($usernameMock);
    }

    public function testFindUserByUsernameIsSuccess()
    {
        // set up fixtures
        // set up fixtures
        $username     = 'test@papalocal.com';
        $usernameMock = $this->createMock(EmailAddress::class);
        $usernameMock->expects($this->once())
                     ->method('getEmailAddress')
                     ->willReturn($username);

        $userId = 1;
        $rolesMock = [
            SecurityRole::ROLE_USER()->getValue(),
            SecurityRole::ROLE_ADMIN()->getValue()
        ];

        $queryMock = $this->createMock(FindBy::class);

        $this->mysqlFacMock->expects($this->once())
                           ->method('newFindBy')
                           ->with($this->equalTo('v_user'), $this->equalTo('username'), $this->equalTo($username))
                           ->willReturn($queryMock);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('setRoles')
            ->with($this->equalTo($rolesMock))
            ->willReturn($userMock);

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('userId'))
            ->willReturn($userId);

        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $recordSetMock->expects($this->once())
            ->method('count')
            ->willReturn(1);
        $recordSetMock->expects($this->exactly(2))
            ->method('current')
            ->willReturn($recordMock);

        $this->userFactoryMock->expects($this->once())
                              ->method('createFromRecord')
                              ->with($this->equalTo($recordMock))
                              ->willReturn($userMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        // exercise SUT
        $userRepo = $this->getMockBuilder(UserRepository::class)
                         ->setConstructorArgs([$this->dataPoolMock, $this->contactProfileHydMock, $this->mysqlBusMock, $this->mysqlFacMock, $this->userFactoryMock])
                         ->setMethodsExcept(['findUserByUsername'])
                         ->getMock();

        $userRepo->expects($this->once())
                 ->method('loadUserRoles')
                 ->with($this->equalTo($userId))
                 ->willReturn($rolesMock);

        $result = $userRepo->findUserByUsername($usernameMock);

        // make assertions
        $this->assertEquals($userMock, $result);
    }
}