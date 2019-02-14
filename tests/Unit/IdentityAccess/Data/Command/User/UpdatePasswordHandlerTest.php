<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/28/18
 * Time: 10:31 PM
 */

namespace Test\Unit\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePassword;
use PapaLocal\IdentityAccess\Data\Command\User\UpdatePasswordHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdatePasswordHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Data\Command\User
 */
class UpdatePasswordHandlerTest extends TestCase
{
    /**
     * @expectedException PapaLocal\Entity\Exception\UserNotFoundException
     */
    public function testHandlerThrowsExceptionWhenUserNotFound()
    {
        // set up fixtures
        $userGuid = 'c5c2a7bc-15a7-4eb9-ac61-a96a0dcf3075';
        
        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $commandMock = $this->createMock(UpdatePassword::class);
        $commandMock->expects($this->exactly(2))
            ->method('getUserGuid')
            ->willReturn($userGuid);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('User'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($userGuid))
            ->willReturn($recordMock);

        // exercise SUT
        $handler = new UpdatePasswordHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $userGuid = 'c5c2a7bc-15a7-4eb9-ac61-a96a0dcf3075';
        $userPass = '$2y$13$4vMXiXa4Vz3mIlNYHw8InugzdvPa7gpoiYB4aXrjGUmFRGrtv.rGK';
        $propsArr = array(
            'id' => 2,
            'personId' => 3,
            'isActive' => 1,
            'notificationSavePoint' => 0,
            'password' => $userPass,
            'timeCreated' => '2018-05-01 10:10:21',
            'timeZone' => 'America/New York',
            'guid' => $userGuid
        );

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
                   ->method('isEmpty')
                   ->willReturn(false);
        $recordMock->expects($this->once())
            ->method('offsetSet')
            ->with($this->equalTo('password'), $this->equalTo($userPass));
        $recordMock->expects($this->once())
            ->method('properties')
            ->willReturn($propsArr);

        $commandMock = $this->createMock(UpdatePassword::class);
        $commandMock->expects($this->once())
                    ->method('getUserGuid')
                    ->willReturn($userGuid);
        $commandMock->expects($this->once())
            ->method('getPassword')
            ->willReturn($userPass);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo('User'));
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->with($this->equalTo($userGuid))
                         ->willReturn($recordMock);

        $tableGatewayMock->expects($this->once())
            ->method('update')
            ->with($this->equalTo($propsArr));

        // exercise SUT
        $handler = new UpdatePasswordHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}