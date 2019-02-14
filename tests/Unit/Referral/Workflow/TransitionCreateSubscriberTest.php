<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/31/18
 * Time: 10:41 PM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Referral\Data\Command\SaveReferral;
use PapaLocal\Referral\Data\MessageFactory;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\Workflow\TransitionCreateSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class TransitionCreateSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class TransitionCreateSubscriberTest extends TestCase
{
    public function testCanCreateReferralWithContactRecipientSuccessfully()
    {
        $commandMock = $this->createMock(SaveReferral::class);

        $contactMock = $this->createMock(ContactRecipient::class);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($contactMock);

        $messageFactoryMock = $this->createMock(MessageFactory::class);
        $messageFactoryMock->expects($this->once())
            ->method('newSaveReferral')
            ->with($referralMock, 'created')
            ->willReturn($commandMock);

        $mysqlBusMock = $this->createMock(MessageBus::class);
        $mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($commandMock);


        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(2))
            ->method('getSubject')
            ->willReturn($referralMock);

        $transitionCreateSubscriber = new TransitionCreateSubscriber($messageFactoryMock, $mysqlBusMock);
        $transitionCreateSubscriber->saveContactReferral($eventMock);
    }

    public function testCanNotCreateReferralWithAgreementRecipient()
    {
        $agreementMock = $this->createMock(AgreementRecipient::class);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->once())
            ->method('getRecipient')
            ->willReturn($agreementMock);

        $messageFactoryMock = $this->createMock(MessageFactory::class);
        $messageFactoryMock->expects($this->never())
            ->method('newSaveReferral');

        $mysqlBusMock = $this->createMock(MessageBus::class);
        $mysqlBusMock->expects($this->never())
            ->method('dispatch');


        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($referralMock);

        $transitionCreateSubscriber = new TransitionCreateSubscriber($messageFactoryMock, $mysqlBusMock);
        $transitionCreateSubscriber->saveContactReferral($eventMock);
    }
}