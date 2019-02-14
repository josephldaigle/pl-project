<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 3:07 PM
 */

namespace Test\Unit\IdentityAccess\Data\Command\User;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\IdentityAccess\Data\Command\User\CreateUser;
use PapaLocal\IdentityAccess\Data\Command\User\CreateUserHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class CreateUserHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Data\Command\User
 */
class CreateUserHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $personGuid  = '530d1136-961f-487c-acf1-be46bd60107f';
        $personId    = 4;
        $firstName   = 'Guy';
        $lastName    = 'Tester';
        $username    = 'gtester@papalocal.com';
        $password    = 'SomeP@$$w0rd12';
        $userGuid    = '352f9ca6-e46a-4cfc-bd17-be4d46bbf0e8';
        $emailId     = 43;
        $emailTypeId = 6; // username email type

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->exactly(4))
                   ->method('offsetGet')
                   ->with($this->equalTo('id'))
                   ->willReturnOnConsecutiveCalls($personId, $personId, $emailId, $emailTypeId);

        $recSetMock = $this->createMock(RecordSetInterface::class);
        $recSetMock->expects($this->exactly(2))
                   ->method('current')
                   ->willReturn($recordMock);

        $commandMock = $this->createMock(CreateUser::class);
        $commandMock->expects($this->exactly(2))
                    ->method('getPersonGuid')
                    ->willReturn($personGuid);
        $commandMock->expects($this->once())
                    ->method('getFirstName')
                    ->willReturn($firstName);
        $commandMock->expects($this->once())
                    ->method('getLastName')
                    ->willReturn($lastName);
        $commandMock->expects($this->once())
                    ->method('getGuid')
                    ->willReturn($userGuid);
        $commandMock->expects($this->once())
                    ->method('getPassword')
                    ->willReturn($password);
        $commandMock->expects($this->exactly(2))
                    ->method('getUsername')
                    ->willReturn($username);


        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(5))
                         ->method('setTable')
                         ->withConsecutive(
                             $this->equalTo('Person'),
                             $this->equalTo('User'),
                             $this->equalTo('L_EmailAddressType'),
                             $this->equalTo('EmailAddress'),
                             $this->equalTo('R_PersonEmailAddress')
                         );

        $tableGatewayMock->expects($this->exactly(4))
                         ->method('create')
                         ->withConsecutive(
                             [
                                 $this->equalTo(array(
                                     'guid'      => $personGuid,
                                     'firstName' => $firstName,
                                     'lastName'  => $lastName,
                                 )),
                             ],
                             [
                                 $this->equalTo(array(
                                     'guid'     => $userGuid,
                                     'personId' => $personId,
                                     'password' => $password,
                                     'timeZone' => 'America/New York'
                                 )),
                             ],
                             [
                                 $this->equalTo(array(
                                     'emailAddress' => $username,
                                 )),
                             ],
                             [
                                 $this->equalTo(array(
                                     'personId' => $personId,
                                     'emailId'  => $emailId,
                                     'typeId'   => $emailTypeId,
                                 )),
                             ]
                         );

        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->with($this->equalTo($personGuid))
                         ->willReturn($recordMock);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('findBy')
                         ->withConsecutive(
                             [$this->equalTo('description'), $this->equalTo('Username')],
                             [$this->equalTo('emailAddress'), $this->equalTo($username)])
                         ->willReturn($recSetMock);

        // exercise SUT
        $handler = new CreateUserHandler($tableGatewayMock);
        $handler->__invoke($commandMock);

        // make assertions
        $this->assertInstanceOf(CreateUserHandler::class, $handler);
    }
}