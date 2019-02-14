<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/12/18
 * Time: 8:37 AM
 */

namespace Test\Unit\Referral\Message\Command;


use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Message\Command\CreateReferral;
use PapaLocal\Referral\Message\Command\CreateReferralHandler;
use PapaLocal\Referral\ReferralService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * Class CreateReferralHandlerTest
 * @package Test\Unit\Referral\Message\Command
 */
class CreateReferralHandlerTest extends TestCase
{
    public function testCreateReferralHandlerIsSuccess()
    {
        // test fixtures
        $commandMock = $this->createMock(CreateReferral::class);
        $referralMock = $this->createMock(Referral::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('denormalize')
            ->willReturn($referralMock);

        $serviceMock = $this->createMock(ReferralService::class);
        $serviceMock->expects($this->once())
            ->method('createReferral')
            ->with($referralMock);

        // Exercise SUT
        $handler = new CreateReferralHandler($serviceMock, $serializerMock);
        $handler->__invoke($commandMock);
    }
}