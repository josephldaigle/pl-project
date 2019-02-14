<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/21/18
 * Time: 7:28 PM
 */

namespace Test\Unit\IdentityAccess\Message\Query\User;


use PapaLocal\Core\Factory\VOFactory;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsername;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsernameHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindUserByUsernameHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Message\Query\User
 */
class FindUserByUsernameHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $username = 'test@papalocal.com';
        $emailAddressMock = $this->createMock(EmailAddress::class);
        $userMock = $this->createMock(User::class);

        $findQryMock = $this->createMock(FindUserByUsername::class);
        $findQryMock->expects($this->once())
            ->method('getUsername')
            ->willReturn($username);

        $userRepoMock = $this->createMock(UserRepository::class);
        $userRepoMock->expects($this->once())
            ->method('findUserByUsername')
            ->with($this->equalTo($emailAddressMock))
            ->willReturn($userMock);

        $voFactoryMock = $this->createMock(VOFactory::class);
        $voFactoryMock->expects($this->once())
            ->method('createEmailAddress')
            ->with($this->equalTo($username), $this->equalTo(EmailAddressType::USERNAME()))
            ->willReturn($emailAddressMock);

        // exercise SUT
        $handler = new FindUserByUsernameHandler($userRepoMock, $voFactoryMock);
        $result = $handler->__invoke($findQryMock);

        // make assertions
        $this->assertEquals($userMock, $result);

    }
}