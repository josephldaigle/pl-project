<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/2/18
 * Time: 9:07 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Command\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PauseAgreement;
use PapaLocal\ReferralAgreement\Message\Command\Agreement\PauseAgreementHandler;
use PapaLocal\ReferralAgreement\ReferralAgreementService;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusChangeReason;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class PauseAgreementHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Command\Agreement
 */
class PauseAgreementHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $agmtGuid = 'A3a726769-ad97-40aa-9fc1-21d8cff36e34';
        $updaterGuid = 'a70c5609-4132-48cd-a561-c2c37b20bdac';
        
        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->exactly(2))
            ->method('value')
            ->willReturnOnConsecutiveCalls($agmtGuid, $updaterGuid);

        $reasonMock = $this->createMock(StatusChangeReason::class);
        $reasonMock->expects($this->once())
            ->method('getValue')
            ->willReturn(StatusChangeReason::OWNER_REQUESTED()->getValue());

        $commandMock = $this->createMock(PauseAgreement::class);
        $commandMock->expects($this->exactly(2))
            ->method('getAgreementGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getChangeReason')
            ->willReturn($reasonMock);
        $commandMock->expects($this->once())
            ->method('getRequestorGuid')
            ->willReturn($guidMock);

        $statusMock = $this->createMock(AgreementStatus::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('denormalize')
            ->with(
                $this->equalTo(
                    [
                        'agreementId' => ['value' => $agmtGuid],
                        'status' => ['value' => Status::INACTIVE()->getValue()],
                        'reason' => ['value' => StatusChangeReason::OWNER_REQUESTED()->getValue()],
                        'updater' => ['value' => $updaterGuid],
                        'timeUpdated' => date('Y-m-d H:i:s', time())
                    ]
                ),
                $this->equalTo(AgreementStatus::class),
                $this->equalTo('array'))
            ->willReturn($statusMock);
        
        $refAgmtSvcMock = $this->createMock(ReferralAgreementService::class);
        $refAgmtSvcMock->expects($this->once())
            ->method('pauseAgreement')
            ->with($this->equalTo($guidMock), $this->equalTo($statusMock));
        

        // exercise SUT
        $handler = new PauseAgreementHandler($serializerMock, $refAgmtSvcMock);
        $handler->__invoke($commandMock);
        
    }
}