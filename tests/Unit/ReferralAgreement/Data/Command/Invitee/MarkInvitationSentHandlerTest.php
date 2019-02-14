<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/1/18
 * Time: 7:07 AM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\MarkInvitationSent;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\MarkInvitationSentHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class MarkInvitationSentHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Invitee
 */
class MarkInvitationSentHandlerTest extends TestCase
{
    public function testHandlerDoesNothingWhenInvitationNotFound()
    {
        $invitationGuid = '47520719-c2de-4430-a557-e165fc76cbcb';

        $invitationRecordMock = $this->createMock(RecordInterface::class);
        $invitationRecordMock->expects($this->once())
                             ->method('isEmpty')
                             ->willReturn(true);
        $invitationRecordMock->expects($this->never())
                             ->method('properties');

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo('ReferralAgreementInvitee'));
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->with($this->equalTo($invitationGuid))
                         ->willReturn($invitationRecordMock);
        $tableGatewayMock->expects($this->never())
                         ->method('update');

        $commandMock = $this->createMock(MarkInvitationSent::class);
        $commandMock->expects($this->once())
                    ->method('getInvitationGuid')
                    ->willReturn($invitationGuid);

        // exercise SUT
        $handler = new MarkInvitationSentHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $invitationGuid = '47520719-c2de-4430-a557-e165fc76cbcb';
        $propsArr = [];

        $invitationRecordMock = $this->createMock(RecordInterface::class);
        $invitationRecordMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);
        $invitationRecordMock->expects($this->once())
            ->method('properties')
            ->willReturn($propsArr);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('ReferralAgreementInvitee'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($invitationGuid))
            ->willReturn($invitationRecordMock);
        $tableGatewayMock->expects($this->once())
            ->method('update')
            ->with($this->equalTo($propsArr));

        $commandMock = $this->createMock(MarkInvitationSent::class);
        $commandMock->expects($this->once())
            ->method('getInvitationGuid')
            ->willReturn($invitationGuid);
        
        // exercise SUT
        $handler = new MarkInvitationSentHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}