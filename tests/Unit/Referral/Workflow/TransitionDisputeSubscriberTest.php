<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/31/18
 * Time: 11:42 PM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Referral\Data\Command\UpdateReferral;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Workflow\TransitionDisputeSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class TransitionDisputeSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class TransitionDisputeSubscriberTest extends TestCase
{
    public function testCanUpdateReferralRateLowerThanThreeStarsSuccessfully()
    {
        $commandMock = $this->createMock(UpdateReferral::class);

        $referralMock = $this->createMock(Referral::class);

        $messageFactoryMock = $this->createMock(MessageFactory::class);
        $messageFactoryMock->expects($this->once())
            ->method('newUpdateReferral')
            ->with($referralMock, 'disputed')
            ->willReturn($commandMock);

        $mysqlBusMock = $this->createMock(MessageBus::class);
        $mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($commandMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($referralMock);

        $transitionAcquireSubscriber = new TransitionDisputeSubscriber($messageFactoryMock, $mysqlBusMock);
        $transitionAcquireSubscriber->updateReferralRate($eventMock);
    }
}