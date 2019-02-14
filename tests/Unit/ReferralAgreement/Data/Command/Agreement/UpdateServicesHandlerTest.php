<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/31/18
 * Time: 11:23 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateServices;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateServicesHandler;
use PapaLocal\ReferralAgreement\ValueObject\ServiceType;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateServicesHandler
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateServicesHandlerTest extends TestCase
{
    public function testUpdateServicesHandlerIsSuccess()
    {
        // set up fixtures
        $agmtId   = 4;
        $agmtGuid = '0cf3625b-8e47-4ff1-a024-a948b57274ca';

        $svcArr = array(
            array('service' => 'Some service', 'type' => ServiceType::INCLUDE ()->getValue()),
            array('service' => 'Another service', 'type' => ServiceType::EXCLUDE()->getValue()),
        );

        $agmtRecordMock = $this->createMock(RecordInterface::class);
        $agmtRecordMock->expects($this->once())
                       ->method('isEmpty')
                       ->willReturn(false);
        $agmtRecordMock->expects($this->exactly(3))
                       ->method('offsetGet')
                       ->withConsecutive(['id'])
                       ->willReturn($agmtId);

        $existingSvcRecMock = $this->createMock(RecordInterface::class);
        $existingSvcRecMock->expects($this->exactly(2))
                           ->method('offsetGet')
                           ->with($this->equalTo('id'))
                           ->willReturnOnConsecutiveCalls(1, 2);

        $servicesRecSetMock = $this->createMock(RecordSetInterface::class);
        $servicesRecSetMock->expects($this->exactly(3))
                           ->method('valid')
                           ->willReturnOnConsecutiveCalls(true, true, false);
        $servicesRecSetMock->expects($this->exactly(2))
                           ->method('current')
                           ->willReturn($existingSvcRecMock);

        $commandMock = $this->createMock(UpdateServices::class);
        $commandMock->expects($this->once())
                    ->method('getAgreementGuid')
                    ->willReturn($agmtGuid);
        $commandMock->expects($this->once())
                    ->method('getServices')
                    ->willReturn($svcArr);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('setTable')
                         ->withConsecutive(['v_referral_agreement'], ['ReferralAgreementService']);

        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->with($this->equalTo($agmtGuid))
                         ->willReturn($agmtRecordMock);

        $tableGatewayMock->expects($this->once())
                         ->method('findBy')
                         ->with($this->equalTo('agreementId'), $this->equalTo($agmtId))
                         ->willReturn($servicesRecSetMock);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('delete')
                         ->withConsecutive($this->equalTo(1), $this->equalTo(2));

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('create')
                         ->withConsecutive(
                             [
                                 $this->equalTo(array(
                                     'agreementId' => $agmtId,
                                     'service'     => $svcArr[0]['service'],
                                     'type'        => $svcArr[0]['type'],
                                 )),
                             ],
                             [
                                 $this->equalTo(array(
                                     'agreementId' => $agmtId,
                                     'service'     => $svcArr[1]['service'],
                                     'type'        => $svcArr[1]['type'],
                                 )),
                             ]
                         );

        // exercise SUT
        $handler = new UpdateServicesHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

    /**
     * @expectedException PapaLocal\Core\Data\Exception\CommandException
     * @expectedExceptionCode 100
     */
    public function testUpdateServicesHandlerThrowsExceptionWhenAgreementNotFound()
    {
        // set up fixtures
        $agmtGuid   = 'd65c3ea7-de07-424e-84a4-0a32b4793f6e';
        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
                   ->method('isEmpty')
                   ->willReturn(true);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable');
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($recordMock);

        $commandMock = $this->createMock(UpdateServices::class);
        $commandMock->expects($this->exactly(2))
                    ->method('getAgreementGuid')
                    ->willReturn($agmtGuid);

        // exercise SUT
        $handler = new UpdateServicesHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}