<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/1/18
 * Time: 12:27 AM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Message\Query\User\FindByGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuid;
use PapaLocal\Notification\Notifier;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Notification\Message\ReferralFinalization;
use PapaLocal\Referral\Notification\Message\ReferralFinalizationConfirmation;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\ValueObject\ReferralRating;
use PapaLocal\Referral\Workflow\EnteredFinalizedSubscriber;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredFinalizedSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class EnteredFinalizedSubscriberTest extends TestCase
{
    public function testFinalizeReferralSendNotificationsSuccessfullyAndDoNotDispatchDisputeResolvedWithContactRecipient()
    {
        $emailMessageBuilderMock = $this->createMock(EmailMessageBuilder::class);
        $emailerMock = $this->createMock(Emailer::class);
        $eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $providerMock = $this->createMock(User::class);
        $contactRecipientGuidMock = $this->createMock(GuidInterface::class);

        $contactRecipientMock = $this->createMock(ContactRecipient::class);
        $contactRecipientMock->expects($this->once())
            ->method('getContactGuid')
            ->willReturn($contactRecipientGuidMock);

        $referralFinalizationMock = $this->createMock(ReferralFinalization::class);
        $referralFinalizationConfirmationMock = $this->createMock(ReferralFinalizationConfirmation::class);

        $findByGuidMock = $this->createMock(FindUserByGuid::class);
        $providerGuidMock = $this->createMock(GuidInterface::class);

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->exactly(2))
            ->method('newFindUserByGuid')
            ->withConsecutive($this->equalTo($contactRecipientGuidMock), $this->equalTo($providerGuidMock))
            ->willReturn($findByGuidMock);

        $contactRecipientUserGuidMock = $this->createMock(GuidInterface::class);

        $contactRecipientUserMock = $this->createMock(User::class);
        $contactRecipientUserMock->expects($this->once())
            ->method('getGuid')
            ->willReturn($contactRecipientUserGuidMock);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->equalTo($findByGuidMock))
            ->willReturnOnConsecutiveCalls($contactRecipientUserMock, $providerMock);

        $notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralFinalization')
            ->with($contactRecipientUserMock, $providerMock)
            ->willReturn($referralFinalizationMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralFinalizationConfirmation')
            ->with($contactRecipientUserMock, $providerMock)
            ->willReturn($referralFinalizationConfirmationMock);

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock->expects($this->exactly(2))
            ->method('sendUserNotification')
            ->withConsecutive(
                [$this->equalTo($contactRecipientUserGuidMock), $this->equalTo($referralFinalizationMock)],
                [$this->equalTo($providerGuidMock), $this->equalTo($referralFinalizationConfirmationMock)]
            );

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(2))
            ->method('getRecipient')
            ->willReturn($contactRecipientMock);
        $referralMock->expects($this->exactly(2))
            ->method('getProviderUserGuid')
            ->willReturn($providerGuidMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(4))
            ->method('getSubject')
            ->willReturn($referralMock);

        $enteredCreatedSubscriber = new EnteredFinalizedSubscriber($emailMessageBuilderMock, $emailerMock, $eventDispatcherMock, $notificationFactoryMock, $notifierMock, $appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $enteredCreatedSubscriber->finalizeReferral($eventMock);
    }

    public function testFinalizeReferralSendNotificationsSuccessfullyAndDispatchDisputeResolvedWithAgreementRecipient()
    {
        $score = 2;

        $agreementOwnerGuidMock = $this->createMock(Guid::class);

        $agreementMock = $this->createMock(ReferralAgreement::class);
        $agreementMock->expects($this->once())
            ->method('getOwnerGuid')
            ->willReturn($agreementOwnerGuidMock);

        $agreementRecipientOwnerGuidMock = $this->createMock(GuidInterface::class);

        $agreementRecipientOwnerMock = $this->createMock(User::class);
        $agreementRecipientOwnerMock->expects($this->exactly(1))
            ->method('getGuid')
            ->willReturn($agreementRecipientOwnerGuidMock);

        $raFindByGuidMock = $this->createMock(\PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid::class);

        $emailMessageBuilderMock = $this->createMock(EmailMessageBuilder::class);
        $emailerMock = $this->createMock(Emailer::class);
        $eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $providerMock = $this->createMock(User::class);
        $agreementRecipientGuidMock = $this->createMock(GuidInterface::class);

        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);
        $agreementRecipientMock->expects($this->exactly(2))
            ->method('getGuid')
            ->willReturn($agreementRecipientGuidMock);

        $referralFinalizationMock = $this->createMock(ReferralFinalization::class);
        $referralFinalizationConfirmationMock = $this->createMock(ReferralFinalizationConfirmation::class);

        $findUserByGuidMock = $this->createMock(FindUserByGuid::class);
        $findByGuidMock = $this->createMock(\PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid::class);
        $providerGuidMock = $this->createMock(GuidInterface::class);

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->exactly(2))
            ->method('newFindUserByGuid')
            ->withConsecutive($this->equalTo($agreementRecipientOwnerGuidMock), $this->equalTo($providerGuidMock))
            ->willReturn($findUserByGuidMock);

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);
        $raMessageFactoryMock->expects($this->once())
            ->method('newFindAgreementByGuid')
            ->with($this->equalTo($agreementRecipientGuidMock))
            ->willReturn($raFindByGuidMock);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive($this->equalTo($findUserByGuidMock), $this->equalTo($findByGuidMock), $this->equalTo($findUserByGuidMock))
            ->willReturnOnConsecutiveCalls($agreementMock, $agreementRecipientOwnerMock, $providerMock);

        $notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralFinalization')
            ->with($agreementRecipientOwnerMock, $providerMock)
            ->willReturn($referralFinalizationMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralFinalizationConfirmation')
            ->with($agreementRecipientOwnerMock, $providerMock)
            ->willReturn($referralFinalizationConfirmationMock);

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock->expects($this->exactly(2))
            ->method('sendUserNotification')
            ->withConsecutive(
                [$this->equalTo($agreementRecipientOwnerGuidMock), $this->equalTo($referralFinalizationMock)],
                [$this->equalTo($providerGuidMock), $this->equalTo($referralFinalizationConfirmationMock)]
            );

        $referralRatingMock = $this->createMock(ReferralRating::class);
        $referralRatingMock->expects($this->once())
            ->method('getScore')
            ->willReturn($score);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(3))
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);
        $referralMock->expects($this->exactly(2))
            ->method('getProviderUserGuid')
            ->willReturn($providerGuidMock);
        $referralMock->expects($this->exactly(2))
            ->method('getRating')
            ->willReturn($referralRatingMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(8))
            ->method('getSubject')
            ->willReturn($referralMock);

        $enteredCreatedSubscriber = new EnteredFinalizedSubscriber($emailMessageBuilderMock, $emailerMock, $eventDispatcherMock, $notificationFactoryMock, $notifierMock, $appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $enteredCreatedSubscriber->finalizeReferral($eventMock);
    }
}