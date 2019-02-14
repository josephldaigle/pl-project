<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/16/18
 */


namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\AdaptedTableGateway;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementName;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementNameHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateNameHandlerTest.
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateNameHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid = 'e30e1f26-3b97-4a0f-b7bf-41f0ad317ca1';
        $agmtName = 'New Test Name';

        $updateCommandMock = $this->createMock(UpdateAgreementName::class);
        $updateCommandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($agmtGuid);
        $updateCommandMock->expects($this->exactly(2))
            ->method('getNewName')
            ->willReturn($agmtName);

        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('name'))
            ->willReturn('Old Test Agreement Name');
        $recordMock->expects($this->once())
            ->method('properties')
            ->willReturn(array());

        $tableGatewayMock = $this->createMock(AdaptedTableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('ReferralAgreement'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($agmtGuid))
            ->willReturn($recordMock);
        $tableGatewayMock->expects($this->once())
            ->method('update')
            ->with($this->equalTo(array()));


        // exercise SUT
        $handler = new UpdateAgreementNameHandler($tableGatewayMock);
        $handler->__invoke($updateCommandMock);
    }
}