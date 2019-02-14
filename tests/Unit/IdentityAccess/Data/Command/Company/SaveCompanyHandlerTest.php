<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/23/18
 * Time: 11:31 PM
 */

namespace Test\Unit\IdentityAccess\Data\Company;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\IdentityAccess\ValueObject\SecurityRole;
use PapaLocal\IdentityAccess\Data\Command\Company\SaveCompany;
use PapaLocal\IdentityAccess\Data\Command\Company\SaveCompanyHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class SaveCompanyHandlerTest
 *
 * @package Test\Unit\IdentityAccess\Data\Company
 */
class SaveCompanyHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $userGuid = 'c85b1b70-b2b2-4034-8b3f-d94751f93ee7';
        $userId      = 14; // user's db row id
        $roleId      = 2;  // company role id
        $companyId   = 12;  // company db row id
        $companyGuid = '715bdb64-c074-4285-8aeb-c8b967565e62';
        $companyName = 'Test Company, LLC';

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->exactly(3))
                   ->method('offsetGet')
                   ->withConsecutive(
                       [$this->equalTo('userId')],
                       [$this->equalTo('id')],
                       [$this->equalTo('id')]
                   )
                   ->willReturnOnConsecutiveCalls(
                       $userId, $companyId, $roleId
                   );

        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $recordSetMock->expects($this->exactly(2))
                      ->method('current')
                      ->willReturn($recordMock);

        $commandMock = $this->createMock(SaveCompany::class);
        $commandMock->expects($this->exactly(2))
                    ->method('getCompanyGuid')
                    ->willReturn($companyGuid);
        $commandMock->expects($this->once())
                    ->method('getName')
                    ->willReturn($companyName);
        $commandMock->expects($this->once())
            ->method('getOwnerUserGuid')
            ->willReturn($userGuid);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);

        $tableGatewayMock->expects($this->exactly(5))
                         ->method('setTable')
                         ->withConsecutive($this->equalTo('Company'), $this->equalTo('v_company'),
                             $this->equalTo('L_UserRole'), $this->equalTo('v_user'),
                             $this->equalTo('R_UserCompanyRole'));

        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($companyGuid))
            ->willReturn($recordMock);

        $tableGatewayMock->expects($this->exactly(2))
            ->method('findBy')
            ->withConsecutive(
                $this->equalTo(['name', SecurityRole::ROLE_COMPANY()->getValue()]),
                $this->equalTo(['userGuid', $userGuid])
            )
            ->willReturn($recordSetMock);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('create')
                         ->withConsecutive(
                             $this->equalTo(
                                 [
                                     'guid' => $companyGuid,
                                     'name' => $companyName,
                                 ]
                             ),
                             $this->equalTo(
                                 [
                                     'userId'    => $userId,
                                     'companyId' => $companyId,
                                     'roleId'    => $roleId,
                                 ]
                             )
                         );

        // exercise SUT
        $handler = new SaveCompanyHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}