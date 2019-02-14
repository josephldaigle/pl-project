<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/5/18
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementStatus;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementStatusHandler;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateAgreementStatusHandlerTest.
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementStatusHandlerTest extends TestCase
{
    /**
     * @expectedException PapaLocal\ReferralAgreement\Exception\AgreementNotFoundException
     */
    public function testSaveStatusChangeThrowsExceptionWhenAgreementNotFound()
    {
        // set up fixtures
        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
                    ->method('isEmpty')
                    ->willReturn(true);

        $updateCmdMock = $this->createMock(UpdateAgreementStatus::class);
        $updateCmdMock->expects($this->exactly(2))
                      ->method('getAgreementGuid')
                      ->willReturn('a648e84b-b090-4e5d-b3d4-064464085170');

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with('ReferralAgreement');
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($agmtRecMock);

        // exercise SUT
        $handler = new UpdateAgreementStatusHandler($tableGatewayMock);
        $handler->__invoke($updateCmdMock);
    }

    /**
     * @expectedException PapaLocal\Core\Data\Exception\CommandException
     * @expectedExceptionCode 100
     */
    public function testSaveStatusChangeThrowsExceptionWhenReasonNotFound()
    {
        // set up fixtures
        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
                    ->method('isEmpty')
                    ->willReturn(false);

        $reasonRecSetMock = $this->createMock(RecordSetInterface::class);
        $reasonRecSetMock->expects($this->once())
                         ->method('count')
                         ->willReturn(0);

        $updateCmdMock = $this->createMock(UpdateAgreementStatus::class);
        $updateCmdMock->expects($this->once())
                      ->method('getAgreementGuid')
                      ->willReturn('a648e84b-b090-4e5d-b3d4-064464085170');
        $updateCmdMock->expects($this->exactly(2))
                      ->method('getReason')
                      ->willReturn('bad reason');

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(2))
                         ->method('setTable')
                         ->withConsecutive(['ReferralAgreement'], ['L_ReferralAgreementStatusReason']);
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($agmtRecMock);
        $tableGatewayMock->expects($this->once())
                         ->method('findBy')
                         ->with('reason')
                         ->willReturn($reasonRecSetMock);

        // exercise SUT
        $handler = new UpdateAgreementStatusHandler($tableGatewayMock);
        $handler->__invoke($updateCmdMock);
    }

    /**
     * @expectedException PapaLocal\Core\Data\Exception\CommandException
     * @expectedExceptionCode 100
     */
    public function testSaveStatusChangeThrowsExceptionWhenAuthorNotFound()
    {
        // set up fixtures
        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
                    ->method('isEmpty')
                    ->willReturn(false);

        $reasonRecSetMock = $this->createMock(RecordSetInterface::class);
        $reasonRecSetMock->expects($this->once())
                         ->method('count')
                         ->willReturn(1);

        $userRecSetMock = $this->createMock(RecordSetInterface::class);
        $userRecSetMock->expects($this->once())
                       ->method('count')
                       ->willReturn(0);

        $authorGuid = '8fd664b4-dc48-4a64-b5a1-db18b56db9b6';

        $updateCmdMock = $this->createMock(UpdateAgreementStatus::class);
        $updateCmdMock->expects($this->once())
                      ->method('getAgreementGuid')
                      ->willReturn('a648e84b-b090-4e5d-b3d4-064464085170');
        $updateCmdMock->expects($this->once())
                      ->method('getReason')
                      ->willReturn(StatusChangeReason::PUBLISHED()->getValue());
        $updateCmdMock->expects($this->exactly(2))
                      ->method('getAuthorGuid')
                      ->willReturn($authorGuid);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(3))
                         ->method('setTable')
                         ->withConsecutive(['ReferralAgreement'], ['L_ReferralAgreementStatusReason'], ['v_user']);
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($agmtRecMock);
        $tableGatewayMock->expects($this->exactly(2))
                         ->method('findBy')
                         ->withConsecutive(
                             ['reason', StatusChangeReason::PUBLISHED()->getValue()],
                             ['userGuid', $authorGuid]
                         )
                         ->willReturnOnConsecutiveCalls($reasonRecSetMock, $userRecSetMock);

        // exercise SUT
        $handler = new UpdateAgreementStatusHandler($tableGatewayMock);
        $handler->__invoke($updateCmdMock);
    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid   = 'a648e84b-b090-4e5d-b3d4-064464085170';
        $agmtRowId  = 20;
        $status     = Status::ACTIVE()->getValue();
        $reasonId   = 15;
        $authorGuid = '8fd664b4-dc48-4a64-b5a1-db18b56db9b6';
        $authorId   = 30;
        $authorId   = 25;

        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
                    ->method('isEmpty')
                    ->willReturn(false);
        $agmtRecMock->expects($this->once())
                    ->method('offsetGet')
                    ->with($this->equalTo('id'))
                    ->willReturn($agmtRowId);

        $reasonRecMock = $this->createMock(RecordInterface::class);
        $reasonRecMock->expects($this->once())
                      ->method('offsetGet')
                      ->with($this->equalTo('id'))
                      ->willReturn($reasonId);
        $reasonRecSetMock = $this->createMock(RecordSetInterface::class);
        $reasonRecSetMock->expects($this->once())
                         ->method('count')
                         ->willReturn(1);
        $reasonRecSetMock->expects($this->once())
                         ->method('current')
                         ->willReturn($reasonRecMock);

        $userRecMock = $this->createMock(RecordInterface::class);
        $userRecMock->expects($this->once())
                    ->method('offsetGet')
                    ->with($this->equalTo('userId'))
                    ->willReturn($authorId);
        $userRecSetMock = $this->createMock(RecordSetInterface::class);
        $userRecSetMock->expects($this->once())
                       ->method('count')
                       ->willReturn(1);
        $userRecSetMock->expects($this->once())
                       ->method('current')
                       ->willReturn($userRecMock);


        $updateCmdMock = $this->createMock(UpdateAgreementStatus::class);
        $updateCmdMock->expects($this->once())
                      ->method('getAgreementGuid')
                      ->willReturn($agmtGuid);
        $updateCmdMock->expects($this->once())
                      ->method('getReason')
                      ->willReturn(StatusChangeReason::PUBLISHED()->getValue());
        $updateCmdMock->expects($this->once())
                      ->method('getStatus')
                      ->willReturn($status);
        $updateCmdMock->expects($this->once())
                      ->method('getAuthorGuid')
                      ->willReturn($authorGuid);


        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(4))
                         ->method('setTable')
                         ->withConsecutive(['ReferralAgreement'], ['L_ReferralAgreementStatusReason'], ['v_user'],
                             ['ReferralAgreementStatus']);
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($agmtRecMock);
        $tableGatewayMock->expects($this->exactly(2))
                         ->method('findBy')
                         ->withConsecutive(
                             [$this->equalTo('reason'), $this->equalTo(StatusChangeReason::PUBLISHED()->getValue())],
                             [$this->equalTo('userGuid'), $this->equalTo($authorGuid)]
                         )
                         ->willReturnOnConsecutiveCalls($reasonRecSetMock, $userRecSetMock);
        $tableGatewayMock->expects($this->once())
                         ->method('create')
                         ->with($this->equalTo(array(
                             'agreementId' => $agmtRowId,
                             'status' => $status,
                             'reasonId' => $reasonId,
                             'updatedBy' => $authorId
                         )))
                         ->willReturn(1);

        // exercise SUT
        $handler = new UpdateAgreementStatusHandler($tableGatewayMock);
        $handler->__invoke($updateCmdMock);
    }
}