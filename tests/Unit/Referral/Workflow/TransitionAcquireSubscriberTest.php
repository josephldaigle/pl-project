<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/31/18
 * Time: 11:05 PM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Referral\Data\Command\SaveReferral;
use PapaLocal\Referral\Data\Command\UpdateReferral;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\Workflow\TransitionAcquireSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class TransitionAcquireSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class TransitionAcquireSubscriberTest extends TestCase
{
    public function testCanAcquireReferralWithAgreementRecipientSuccessfully()
    {
        $commandMock = $this->createMock(SaveReferral::class);

        $agreementMock = $this->createMock(AgreementRecipient::class);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(2))
            ->method('getRecipient')
            ->willReturn($agreementMock);

        $messageFactoryMock = $this->createMock(MessageFactory::class);
        $messageFactoryMock->expects($this->once())
            ->method('newSaveReferral')
            ->with($referralMock, 'acquired')
            ->willReturn($commandMock);
        $messageFactoryMock->expects($this->never())
            ->method('newUpdateReferral');

        $mysqlBusMock = $this->createMock(MessageBus::class);
        $mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($commandMock);


        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);


        $transitionAcquireSubscriber = new TransitionAcquireSubscriber($messageFactoryMock, $mysqlBusMock);
        $transitionAcquireSubscriber->acquireReferral($eventMock);
    }

    public function testCanAcquireReferralWithContactRecipientSuccessfully()
    {
        $commandMock = $this->createMock(UpdateReferral::class);

        $contactMock = $this->createMock(ContactRecipient::class);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(2))
            ->method('getRecipient')
            ->willReturn($contactMock);

        $messageFactoryMock = $this->createMock(MessageFactory::class);
        $messageFactoryMock->expects($this->never())
            ->method('newSaveReferral');
        $messageFactoryMock->expects($this->once())
            ->method('newUpdateReferral')
            ->with($referralMock, 'acquired')
            ->willReturn($commandMock);

        $mysqlBusMock = $this->createMock(MessageBus::class);
        $mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($commandMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);


        $transitionAcquireSubscriber = new TransitionAcquireSubscriber($messageFactoryMock, $mysqlBusMock);
        $transitionAcquireSubscriber->acquireReferral($eventMock);
    }
}