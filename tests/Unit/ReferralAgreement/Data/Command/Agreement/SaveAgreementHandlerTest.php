<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/31/18
 * Time: 9:36 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Agreement;


use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\SaveAgreement;
use PapaLocal\ReferralAgreement\Data\Command\Agreement\SaveAgreementHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class SaveAgreementHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Agreement
 */
class SaveAgreementHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $commandMock = $this->createMock(SaveAgreement::class);
        $commandMock->expects($this->once())
            ->method('getGuid')
            ->willReturn('');
        $commandMock->expects($this->once())
            ->method('getName')
            ->willReturn('Test Agreement');
        $commandMock->expects($this->once())
            ->method('getDescription')
            ->willReturn('A test agreement');
        $commandMock->expects($this->once())
            ->method('getStrategy')
            ->willReturn('weekly');
        $commandMock->expects($this->once())
            ->method('getQuantity')
            ->willReturn(5);
        $commandMock->expects($this->once())
            ->method('getBid')
            ->willReturn(30.00);
        $commandMock->expects($this->once())
            ->method('getCompanyGuid')
            ->willReturn('');
        $commandMock->expects($this->once())
            ->method('getOwnerGuid')
            ->willReturn('');

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);

        $tableGatewayMock->expects($this->once())
                     ->method('setTable')
                     ->with($this->equalTo('ReferralAgreement'));

        $tableGatewayMock->expects($this->once())
                     ->method('create');

        // exercise SUT
        $handler = new SaveAgreementHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}