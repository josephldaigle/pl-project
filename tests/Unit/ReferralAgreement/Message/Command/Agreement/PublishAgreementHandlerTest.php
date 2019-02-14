<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/21/18
 */

namespace Test\Unit\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PublishAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PublishAgreementHandler;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PHPUnit\Framework\TestCase;


/**
 * Class PublishAgreementHandlerTest.
 *
 * @package Test\Unit\ReferralAgreement\Message\Command\Agreement
 */
class PublishAgreementHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $commandMock = $this->createMock(PublishAgreement::class);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($guidMock);

        $refAgmtSvcMock = $this->createMock(ReferralAgreementService::class);
        $refAgmtSvcMock->expects($this->once())
            ->method('publishAgreement')
            ->with($this->equalTo($guidMock));

        // exercise SUT
        $handler = new PublishAgreementHandler($refAgmtSvcMock);
        $handler->__invoke($commandMock);
    }
}