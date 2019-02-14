<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 12:03 AM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateLocations;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateLocationsHandler;
use PapaLocal\ReferralAgreement\ValueObject\LocationType;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateLocationsHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateLocationsHandlerTest extends TestCase
{
    /**
     * @expectedException PapaLocal\Core\Data\Exception\CommandException
     * @expectedExceptionCode 100
     */
    public function testHandlerThrowsExceptionWhenAgreementNotFound()
    {
        // set up fixtures
        $commandMock = $this->createMock(UpdateLocations::class);
        $commandMock->expects($this->exactly(2))
                    ->method('getAgreementGuid')
                    ->willReturn('7eda73d7-1f99-4b7b-b944-7b33f6f553e6');

        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
                    ->method('isEmpty')
                    ->willReturn(true);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo('v_referral_agreement'));
        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->willReturn($agmtRecMock);

        // exercise SUT
        $handler = new UpdateLocationsHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid = '59305568-434b-41a8-9aae-a3b0274bff1a';
        $agmtId   = 5;

        $locArr = array(
            array('location' => 'Somewhere, GA', 'type' => LocationType::INCLUDE ()->getValue()),
            array('location' => 'Anywhere, GA', 'type' => LocationType::EXCLUDE()->getValue()),
        );

        $commandMock = $this->createMock(UpdateLocations::class);
        $commandMock->expects($this->once())
                    ->method('getAgreementGuid')
                    ->willReturn($agmtGuid);
        $commandMock->expects($this->once())
                    ->method('getLocations')
                    ->willReturn($locArr);

        $agmtRecordMock = $this->createMock(RecordInterface::class);
        $agmtRecordMock->expects($this->once())
                       ->method('isEmpty')
                       ->willReturn(false);
        $agmtRecordMock->expects($this->exactly(3))
                       ->method('offsetGet')
                       ->with($this->equalTo('id'))
                       ->willReturn($agmtId);

        $locRecMock = $this->createMock(RecordInterface::class);
        $locRecMock->expects($this->exactly(2))
                   ->method('offsetGet')
                   ->with($this->equalTo('id'))
                   ->willReturnOnConsecutiveCalls(1, 2);

        $locationRecordsMock = $this->getMockBuilder
        (RecordSetInterface::class)
                                    ->setMethods(['valid', 'current'])
                                    ->getMockForAbstractClass();
        $locationRecordsMock->expects($this->exactly(3))
                            ->method('valid')
                            ->willReturnOnConsecutiveCalls(true, true, false);
        $locationRecordsMock->expects($this->exactly(2))
                            ->method('current')
                            ->willReturnOnConsecutiveCalls($locRecMock, $locRecMock);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('setTable')
                         ->withConsecutive(['v_referral_agreement'], ['ReferralAgreementLocation']);

        $tableGatewayMock->expects($this->once())
                         ->method('findByGuid')
                         ->with($this->equalTo($agmtGuid))
                         ->willReturn($agmtRecordMock);

        $tableGatewayMock->expects($this->once())
                         ->method('findBy')
                         ->with($this->equalTo('agreementId'), $this->equalTo($agmtId))
                         ->willReturn($locationRecordsMock);

        $tableGatewayMock->expects($this->exactly(2))
                         ->method('delete')
                         ->withConsecutive([$this->equalTo(1)], [$this->equalTo(2)]);

        $tableGatewayMock->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [
                    $this->equalTo(
                        array(
                            'agreementId' => $agmtId,
                            'location' => $locArr[0]['location'],
                            'type' => $locArr[0]['type']
                        ))
                ],
                [
                    $this->equalTo(array(
                        'agreementId' => $agmtId,
                        'location' => $locArr[1]['location'],
                        'type' => $locArr[1]['type']
                    ))
                ]
            );

        // exercise SUT
        $handler = new UpdateLocationsHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

}