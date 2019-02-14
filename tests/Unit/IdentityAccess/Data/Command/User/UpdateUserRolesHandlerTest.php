<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 3:35 PM
 */

namespace Test\Unit\IdentityAccess\Data\Command\User;


use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateUserRoles;
use PapaLocal\IdentityAccess\Data\Command\User\UpdateUserRolesHandler;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateUserRolesHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Data\Command\User
 */
class UpdateUserRolesHandlerTest extends TestCase
{
    /**
     * @expectedException PapaLocal\Entity\Exception\UserNotFoundException
     */
    public function testHandlerThrowsExceptionWhenUserNotFound()
    {
        // set up fixtures
        $userGuid       = '0f7176cf-d548-4515-8522-30488795f536';

        $recSetMock = $this->createMock(RecordSetInterface::class);
        $recSetMock->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $commandMock = $this->createMock(UpdateUserRoles::class);
        $commandMock->expects($this->exactly(2))
            ->method('getUserGuid')
            ->willReturn($userGuid);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('v_user'));
        $tableGatewayMock->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('userGuid'), $this->equalTo($userGuid))
            ->willReturn($recSetMock);

        // exercise SUT
        $handler = new UpdateUserRolesHandler($tableGatewayMock);
        $handler->__invoke($commandMock);

    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $userId         = 2;
        $userGuid       = '0f7176cf-d548-4515-8522-30488795f536';
        $roleUserId     = 5;
        $roleAdminId    = 3;
        $existingRoleId = 4;
        $newRoles       = [
            SecurityRole::ROLE_USER()->getValue(),
            SecurityRole::ROLE_ADMIN()->getValue(),
        ];

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->exactly(6))
                   ->method('offsetGet')
                   ->withConsecutive(
                       [$this->equalTo('userId')],
                       [$this->equalTo('id')],
                       [$this->equalTo('name')],
                       [$this->equalTo('id')],
                       [$this->equalTo('name')],
                       [$this->equalTo('id')]

                   )
                   ->willReturnOnConsecutiveCalls($userId, $existingRoleId, $newRoles[0], $roleUserId, $newRoles[1],
                       $roleAdminId);

        $recSetMock = $this->createMock(RecordSetInterface::class);
        $recSetMock->expects($this->once())
            ->method('count')
            ->willReturnOnConsecutiveCalls(1);
        $recSetMock->expects($this->exactly(6))
                   ->method('current')
                   ->willReturn($recordMock);

        $recSetMock->expects($this->exactly(8))
                   ->method('valid')
                   ->willReturnOnConsecutiveCalls(true, false, true, true, false, true, true, false);

        $commandMock = $this->createMock(UpdateUserRoles::class);
        $commandMock->expects($this->once())
                    ->method('getUserGuid')
                    ->willReturn($userGuid);
        $commandMock->expects($this->once())
                    ->method('getRoles')
                    ->willReturn($newRoles);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(3))
                         ->method('setTable')
                         ->withConsecutive(
                             [$this->equalTo('v_user')],
                             [$this->equalTo('L_UserRole')],
                             [$this->equalTo('R_UserApplicationRole')]
                         );

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('findBy')
                         ->withConsecutive(
                             [$this->equalTo('userGuid'), $this->equalTo($userGuid)],
                             [$this->equalTo('userId'), $this->equalTo($userId)]
                         )
                         ->willReturn($recSetMock);
        $tableGatewayMock->expects($this->once())
                         ->method('findAllOrderedById')
                         ->willReturn($recSetMock);
        $tableGatewayMock->expects($this->once())
                         ->method('delete')
                         ->with($this->equalTo($existingRoleId));
        $tableGatewayMock->expects($this->exactly(2))
                         ->method('create')
                         ->withConsecutive(
                             [
                                 $this->equalTo(array(
                                     'userId' => $userId,
                                     'roleId' => $roleUserId,
                                 )),
                             ],
                             [
                                 $this->equalTo(array(
                                     'userId' => $userId,
                                     'roleId' => $roleAdminId,
                                 )),
                             ]
                         );

        // exercise SUT
        $handler = new UpdateUserRolesHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}