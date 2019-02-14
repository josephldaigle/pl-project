<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/6/18
 * Time: 7:54 AM
 */

namespace Test\Unit\Referral\Message\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Form\ReferralRate;
use PapaLocal\Referral\Message\Command\RateReferral;
use PapaLocal\Referral\Message\Command\RateReferralHandler;
use PapaLocal\Referral\ReferralService;
use PapaLocal\Referral\ValueObject\ReferralRating;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class RateReferralHandlerTest
 * @package Test\Unit\Referral\Message\Command
 */
class RateReferralHandlerTest extends TestCase
{
    public function testRateReferralHandlerIsSuccess()
    {
        // Fixtures
        $referralGuid = 'f0d90dcf-f9b8-4655-bc63-5e7438f46dbc';
        $score = 3;
        $feedback = 'This is a test feedback';

        $guidMock = $this->createMock(Guid::class);

        $referralFormMock = $this->createMock(ReferralRate::class);
        $referralFormMock->expects($this->once())
            ->method('getReferralGuid')
            ->willReturn($guidMock);
        $referralFormMock->expects($this->once())
            ->method('getReferralRate')
            ->willReturn($score);
        $referralFormMock->expects($this->once())
            ->method('getReferralFeedback')
            ->willReturn($feedback);

        $referralRatingMock = $this->createMock(ReferralRating::class);

        $commandMock = $this->createMock(RateReferral::class);
        $commandMock->expects($this->exactly(3))
            ->method('getReferralRate')
            ->willReturn($referralFormMock);


        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('setRating')
            ->with($referralRatingMock);


        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($guidMock, $referralRatingMock);

        $referralServiceMock = $this->createMock(ReferralService::class);
        $referralServiceMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($guidMock))
            ->willReturn($referralMock);
        $referralServiceMock->expects($this->once())
            ->method('rateReferral')
            ->with($referralMock);


        // Exercise SUT
        $handler = new RateReferralHandler($referralServiceMock, $serializerMock);
        $handler->__invoke($commandMock);
    }
}