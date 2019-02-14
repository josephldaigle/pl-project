<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 12:10 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\AdaptedTableGateway;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\UpdateDescriptionHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateDescriptionHandler
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement\
 */
class UpdateDescriptionHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid = '137beda4-9f71-4416-bf9b-83a577ef3068';

        $guidMock = $this->createMock(GuidInterface::class);

        $description = 'New test agreement description';
        
        $recordMock = $this->createMock(RecordInterface::class);
        $recordMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('description'));
        $recordMock->expects($this->once())
            ->method('properties')
            ->willReturn([]);


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
            ->with($this->equalTo([]));

        
        $commandMock = $this->createMock(UpdateDescription::class);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($agmtGuid);
        $commandMock->expects($this->exactly(2))
            ->method('getDescription')
            ->willReturn($description);


        // exercise SUT
        $handler = new UpdateDescriptionHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}