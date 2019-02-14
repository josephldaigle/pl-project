<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/15/18
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\AdaptedTableGateway;
use PapaLocal\Core\Data\Record;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementName;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateAgreementNameHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateAgreementNameHandlerTest.
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class UpdateAgreementNameHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid = '0452346e-9279-477b-a3db-ec116dcdd008';
        $newName = 'New Test Agreement Name';

        $recPropMock = array();

        $recordMock = $this->createMock(Record::class);
        $recordMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('name'))
            ->willReturn('Old Test Agreement Name');
        $recordMock->expects($this->once())
            ->method('properties')
            ->willReturn($recPropMock);

        $tableGatewayMock = $this->createMock(AdaptedTableGateway::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('ReferralAgreement'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($agmtGuid))
            ->willReturn($recordMock);

        $commandMock = $this->createMock(UpdateAgreementName::class);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($agmtGuid);
        $commandMock->expects($this->exactly(2))
            ->method('getNewName')
            ->willReturn($newName);

        // exercise SUT
        $handler = new UpdateAgreementNameHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

}