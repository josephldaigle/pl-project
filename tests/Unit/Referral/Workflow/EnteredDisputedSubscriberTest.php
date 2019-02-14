<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/1/18
 * Time: 12:18 AM
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
use PapaLocal\Notification\ValueObject\AssociateFeedItem;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Notification\Message\ReferralDispute;
use PapaLocal\Referral\Notification\Message\ReferralDisputeConfirmation;
use PapaLocal\Referral\Notification\Message\ReferralDisputeNotice;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\Workflow\EnteredDisputedSubscriber;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredDisputedSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class EnteredDisputedSubscriberTest extends TestCase
{
    public function testDisputeReferralSendNotificationsSuccessfullyAndDoNotDispatchReferralDisputedWithContactRecipient()
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

        $referralDisputeMock = $this->createMock(ReferralDispute::class);
        $referralDisputeConfirmationMock = $this->createMock(ReferralDisputeConfirmation::class);

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

        $sysAdminGuidMock = $this->createMock(Guid::class);
        $sysAdminNotificationMock = $this->createMock(ReferralDisputeNotice::class);
        $associateFeedItemMock = $this->createMock(AssociateFeedItem::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($associateFeedItemMock, $sysAdminGuidMock);

        $notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDispute')
            ->with($contactRecipientUserMock, $providerMock)
            ->willReturn($referralDisputeMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDisputeConfirmation')
            ->with($contactRecipientUserMock, $providerMock)
            ->willReturn($referralDisputeConfirmationMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDisputeNotice')
            ->with($associateFeedItemMock)
            ->willReturn($sysAdminNotificationMock);

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock->expects($this->exactly(3))
            ->method('sendUserNotification')
            ->withConsecutive(
                [$this->equalTo($contactRecipientUserGuidMock), $this->equalTo($referralDisputeMock)],
                [$this->equalTo($providerGuidMock), $this->equalTo($referralDisputeConfirmationMock)],
                [$this->equalTo($sysAdminGuidMock), $this->equalTo($sysAdminNotificationMock)]
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
        $eventMock->expects($this->exactly(5))
            ->method('getSubject')
            ->willReturn($referralMock);

        $enteredCreatedSubscriber = new EnteredDisputedSubscriber($emailMessageBuilderMock, $emailerMock, $eventDispatcherMock, $notificationFactoryMock, $notifierMock, $appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock, $serializerMock);
        $enteredCreatedSubscriber->disputeReferral($eventMock);
    }

    public function testDisputeReferralSendNotificationsSuccessfullyAndDispatchReferralDisputedWithAgreementRecipient()
    {
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
        $agreementRecipientMock->expects($this->exactly(1))
            ->method('getGuid')
            ->willReturn($agreementRecipientGuidMock);

        $referralDisputeMock = $this->createMock(ReferralDispute::class);
        $referralDisputeConfirmationMock = $this->createMock(ReferralDisputeConfirmation::class);

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

        $sysAdminGuidMock = $this->createMock(Guid::class);
        $sysAdminNotificationMock = $this->createMock(ReferralDisputeNotice::class);
        $associateFeedItemMock = $this->createMock(AssociateFeedItem::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->exactly(2))
            ->method('denormalize')
            ->willReturnOnConsecutiveCalls($associateFeedItemMock, $sysAdminGuidMock);

        $notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDispute')
            ->with($agreementRecipientOwnerMock, $providerMock)
            ->willReturn($referralDisputeMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDisputeConfirmation')
            ->with($agreementRecipientOwnerMock, $providerMock)
            ->willReturn($referralDisputeConfirmationMock);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralDisputeNotice')
            ->with($associateFeedItemMock)
            ->willReturn($sysAdminNotificationMock);

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock->expects($this->exactly(3))
            ->method('sendUserNotification')
            ->withConsecutive(
                [$this->equalTo($agreementRecipientOwnerGuidMock), $this->equalTo($referralDisputeMock)],
                [$this->equalTo($providerGuidMock), $this->equalTo($referralDisputeConfirmationMock)],
                [$this->equalTo($sysAdminGuidMock), $this->equalTo($sysAdminNotificationMock)]
            );

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(2))
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);
        $referralMock->expects($this->exactly(2))
            ->method('getProviderUserGuid')
            ->willReturn($providerGuidMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(5))
            ->method('getSubject')
            ->willReturn($referralMock);

        $enteredCreatedSubscriber = new EnteredDisputedSubscriber($emailMessageBuilderMock, $emailerMock, $eventDispatcherMock, $notificationFactoryMock, $notifierMock, $appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock, $serializerMock);
        $enteredCreatedSubscriber->disputeReferral($eventMock);
    }
}