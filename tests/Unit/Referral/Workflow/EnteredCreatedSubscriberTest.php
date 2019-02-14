<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 11/1/18
 * Time: 12:04 AM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\Notification\EmailMessageBuilder;
use PapaLocal\Core\Notification\EmailMessageInterface;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Data\Ewebify;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\IdentityAccess\Message\Query\User\FindByGuid;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByGuid;
use PapaLocal\Notification\Notifier;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\Notification\Message\ReferralInvitationConfirmation;
use PapaLocal\Referral\Notification\NotificationFactory;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\Workflow\EnteredCreatedSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class EnteredCreatedSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class EnteredCreatedSubscriberTest extends TestCase
{
    public function testCreateReferralSendEmailSuccessfullyWhenRecipientIsContact()
    {

        $subject = 'Your Business Has Received A New Referral From  .';
        $sender = Ewebify::ADMIN_EMAIL;
        $emailAddress = 'yacouba@ewebify.com';
        $message = 'emails/referral/referralInvitation.html.twig';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);

        $emailMock = $this->createMock(EmailMessageInterface::class);

        $emailMessageBuilderMock = $this->createMock(EmailMessageBuilder::class);
        $emailMessageBuilderMock->expects($this->once())
            ->method('subject')
            ->with($subject)
            ->willReturn($emailMessageBuilderMock);
        $emailMessageBuilderMock->expects($this->once())
            ->method('from')
            ->with($sender)
            ->willReturn($emailMessageBuilderMock);
        $emailMessageBuilderMock->expects($this->once())
            ->method('sendTo')
            ->with($emailAddress)
            ->willReturn($emailMessageBuilderMock);
        $emailMessageBuilderMock->expects($this->once())
            ->method('usingTwigTemplate')
            ->with($message)
            ->willReturn($emailMessageBuilderMock);
        $emailMessageBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($emailMock);

        $emailerMock = $this->createMock(Emailer::class);
        $emailerMock->expects($this->once())
            ->method('send')
            ->with($this->equalTo($emailMock));


        $referralInvitationConfirmation = $this->createMock(ReferralInvitationConfirmation::class);
        $providerMock = $this->createMock(User::class);

        $contactRecipientMock = $this->createMock(ContactRecipient::class);
        $contactRecipientMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);

        $notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $notificationFactoryMock->expects($this->once())
            ->method('newReferralInvitationConfirmation')
            ->with($contactRecipientMock, $providerMock)
            ->willReturn($referralInvitationConfirmation);

        $providerGuidMock = $this->createMock(Guid::class);

        $notifierMock = $this->createMock(Notifier::class);
        $notifierMock->expects($this->once())
            ->method('sendUserNotification')
            ->with($providerGuidMock, $referralInvitationConfirmation);

        $findByGuidMock = $this->createMock(FindUserByGuid::class);


        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($findByGuidMock)
            ->willReturn($providerMock);


        $iaMessageFactoryMock = $this->createMock(MessageFactory::class);
        $iaMessageFactoryMock->expects($this->once())
            ->method('newFindUserByGuid')
            ->with($providerGuidMock)
            ->willReturn($findByGuidMock);


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

        $enteredCreatedSubscriber = new EnteredCreatedSubscriber($emailMessageBuilderMock, $emailerMock, $notificationFactoryMock, $notifierMock, $appBusMock, $iaMessageFactoryMock);
        $enteredCreatedSubscriber->createReferral($eventMock);
    }
}