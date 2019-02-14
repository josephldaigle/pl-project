<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/15/18
 */

namespace Test\Unit\IdentityAccess\Message\Query\User;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Data\UserRepository;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuidHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByGuidHandlerTest.
 *
 * @package Test\Unit\IdentityAccess\Message\Query\User
 */
class FindUserByGuidHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $userGuidMock = $this->createMock(GuidInterface::class);
        $userMock = $this->createMock(User::class);

       $userRepoMock = $this->createMock(UserRepository::class);
       $userRepoMock->expects($this->once())
           ->method('findUserByGuid')
           ->with($this->equalTo($userGuidMock))
           ->willReturn($userMock);


        $findByGuidQryMock = $this->createMock(FindUserByGuid::class);
        $findByGuidQryMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($userGuidMock);

        // exercise SUT
        $handler = new FindUserByGuidHandler($userRepoMock);
        $result = $handler->__invoke($findByGuidQryMock);

        // make assertions
        $this->assertEquals($userMock, $result);
    }
}