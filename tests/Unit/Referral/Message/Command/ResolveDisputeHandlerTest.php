<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/7/18
 * Time: 12:05 PM
 */

namespace Test\Unit\Referral\Message\Command;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Form\DisputeResolution;
use PapaLocal\Referral\Message\Command\ResolveDispute;
use PapaLocal\Referral\Message\Command\ResolveDisputeHandler;
use PapaLocal\Referral\ReferralService;
use PapaLocal\Referral\ValueObject\ReferralRating;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class ResolveDisputeHandlerTest
 * @package Test\Unit\Referral\Message\Command
 */
class ResolveDisputeHandlerTest extends TestCase
{
    public function testResolveDisputeHandlerIsSuccess()
    {
        $referralScore = 4;
        $referralRatingNote = 'This is a rating note.';
        $referralResolution = 'accepted';
        $referralReviewerNote = 'This is a reviewer note.';

        $guidMock = $this->createMock(Guid::class);

        $reviewerGuidMock = $this->createMock(Guid::class);

        $referralRatingMock = $this->createMock(ReferralRating::class);
        $referralRatingMock->expects($this->once())
            ->method('getScore')
            ->willReturn($referralScore);
        $referralRatingMock->expects($this->once())
            ->method('getRatingNote')
            ->willReturn($referralRatingNote);
        $referralRatingMock->expects($this->once())
            ->method('setReviewerGuid')
            ->with($reviewerGuidMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(2))
            ->method('getRating')
            ->willReturn($referralRatingMock);
        $referralMock->expects($this->once())
            ->method('setRating')
            ->with($referralRatingMock);

        $disputeResolutionMock = $this->createMock(DisputeResolution::class);
        $disputeResolutionMock->expects($this->once())
            ->method('getReferralGuid')
            ->willReturn($guidMock);
        $disputeResolutionMock->expects($this->once())
            ->method('getResolution')
            ->willReturn($referralResolution);
        $disputeResolutionMock->expects($this->once())
            ->method('getReviewerNote')
            ->willReturn($referralReviewerNote);

        $serviceMock = $this->createMock(ReferralService::class);
        $serviceMock->expects($this->once())
            ->method('findByGuid')
            ->with($guidMock)
            ->willReturn($referralMock);
        $serviceMock->expects($this->once())
            ->method('resolveDispute')
            ->with($referralMock);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
//            ->withConsecutive(
//                [$this->equalTo(array('value' => $referralGuid)), $this->equalTo(Guid::class), $this->equalTo('array')],
//                [$this->equalTo(array(
//                    'score' => $referralScore,
//                    'ratingNote' => $referralRatingNote,
//                    'resolution' => $referralResolution,
//                    'reviewerNote' => $referralReviewerNote
//                )), $this->equalTo(ReferralRating::class), $this->equalTo('array')]
//            )
            ->willReturnOnConsecutiveCalls($guidMock, $referralRatingMock);

        $commandMock = $this->createMock(ResolveDispute::class);
        $commandMock->expects($this->exactly(3))
            ->method('getDisputeResolution')
            ->willReturn($disputeResolutionMock);
        $commandMock->expects($this->once())
            ->method('getReviewerGuid')
            ->willReturn($reviewerGuidMock);

        $resolveDispute = new ResolveDisputeHandler($serviceMock, $serializerMock);
        $resolveDispute->__invoke($commandMock);
    }
}