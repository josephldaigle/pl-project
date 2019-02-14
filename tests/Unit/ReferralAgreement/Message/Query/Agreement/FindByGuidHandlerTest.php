<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/15/18
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuidHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByGuidHandlerTest.
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Agreement
 */
class FindByGuidHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuidMock = $this->createMock(GuidInterface::class);
        $agmtMock = $this->createMock(ReferralAgreement::class);

        $refAgmtRepoMock = $this->createMock(ReferralAgreementRepository::class);
        $refAgmtRepoMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($agmtGuidMock))
            ->willReturn($agmtMock);
        $refAgmtRepoMock->expects($this->once())
            ->method('getCurrentPeriodReferralCount')
            ->with($this->equalTo($agmtGuidMock))
            ->willReturn(5);

        $inviteeRepoMock = $this->createMock(InviteeRepository::class);
        $inviteeRepoMock->expects($this->once())
            ->method('findAllByAgreementGuid')
            ->with($this->equalTo($agmtGuidMock));

        $findByGuidQryMock = $this->createMock(FindByGuid::class);
        $findByGuidQryMock->expects($this->exactly(3))
            ->method('getAgreementGuid')
            ->willReturn($agmtGuidMock);

        // exercise SUT
        $handler = new FindByGuidHandler($refAgmtRepoMock, $inviteeRepoMock);
        $handler->__invoke($findByGuidQryMock);
    }
}