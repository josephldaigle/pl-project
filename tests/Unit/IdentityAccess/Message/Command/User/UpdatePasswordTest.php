<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/28/18
 * Time: 10:18 PM
 */

namespace Test\Unit\IdentityAccess\Message\Command\User;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\IdentityAccess\Message\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Message\Command\User\UpdatePasswordHandler;
use PapaLocal\IdentityAccess\Service\UserService;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdatePasswordTest
 *
 * @package Test\Unit\IdentityAccess\Message\Command\User
 */
class UpdatePasswordTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $userPassword = 'NewP@SSw0rd1!';

        $commandMock = $this->createMock(UpdatePassword::class);
        $commandMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getPassword')
            ->willReturn($userPassword);
        
        $userSvcMock = $this->createMock(UserService::class);
        $userSvcMock->expects($this->once())
            ->method('updatePassword')
            ->with($this->equalTo($guidMock), $this->equalTo($userPassword));

        // exercise SUT
        $handler = new UpdatePasswordHandler($userSvcMock);
        $handler->__invoke($commandMock);
    }
}