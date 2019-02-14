<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 10:46 AM
 */

namespace Test\Unit\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateDescription;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\UpdateDescriptionHandler;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PHPUnit\Framework\TestCase;


/**
 * Class UpdateDescriptionHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Command\Agreement
 */
class UpdateDescriptionHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $description = 'New test agreement description';

        $commandMock = $this->createMock(UpdateDescription::class);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getAgreementDescription')
            ->willReturn($description);

        $serviceMock = $this->createMock(ReferralAgreementService::class);
        $serviceMock->expects($this->once())
            ->method('updateAgreementDescription')
            ->with($this->equalTo($guidMock), $this->equalTo($description));

        // exercise SUT
        $handler = new UpdateDescriptionHandler($serviceMock);
        $handler->__invoke($commandMock);
    }
}