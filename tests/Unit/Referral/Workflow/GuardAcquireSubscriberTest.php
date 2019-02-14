<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 10/31/18
 * Time: 8:51 AM
 */

namespace Test\Unit\Referral\Workflow;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsername;
use PapaLocal\Referral\Entity\Referral;
use PapaLocal\Referral\ValueObject\AgreementRecipient;
use PapaLocal\Referral\ValueObject\ContactRecipient;
use PapaLocal\Referral\Workflow\GuardAcquireSubscriber;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Message\MessageFactory;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\FindByGuid;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\Status;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * Class GuardAcquireSubscriberTest
 * @package Test\Unit\Referral\Workflow
 */
class GuardAcquireSubscriberTest extends TestCase
{
    public function testConfirmCanAcquireReferralSetsTransitionBlockerWhenAgreementIsInactive()
    {
        $agreementGuidMock = $this->createMock(Guid::class);

        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);
        $agreementRecipientMock->expects($this->once())
            ->method('getGuid')
            ->willReturn($agreementGuidMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(3))
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);

        $agreementStatusMock = $this->createMock(AgreementStatus::class);
        $agreementStatusMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(Status::INACTIVE());

        $agreementHistoryMock = $this->createMock(StatusHistory::class);
        $agreementHistoryMock->expects($this->once())
            ->method('getCurrentStatus')
            ->willReturn($agreementStatusMock);

        $agreementMock = $this->createMock(ReferralAgreement::class);
        $agreementMock->expects($this->once())
            ->method('getStatusHistory')
            ->willReturn($agreementHistoryMock);

        $findByGuidMock = $this->createMock(FindByGuid::class);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($findByGuidMock)
            ->willReturn($agreementMock);

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->never())
            ->method('newFindUserByUsername');

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);
        $raMessageFactoryMock->expects($this->once())
            ->method('newFindAgreementByGuid')
            ->with($agreementGuidMock)
            ->willReturn($findByGuidMock);

        $guardEventMock = $this->createMock(GuardEvent::class);
        $guardEventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);
        $guardEventMock->expects($this->once())
            ->method('addTransitionBlocker');

        $guardMock = new GuardAcquireSubscriber($appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $guardMock->confirmCanAcquireReferral($guardEventMock);
    }

    public function testConfirmCanAcquireReferralDoesNotSetTransitionBlockerWhenAgreementIsActive()
    {
        $agreementGuidMock = $this->createMock(Guid::class);

        $agreementRecipientMock = $this->createMock(AgreementRecipient::class);
        $agreementRecipientMock->expects($this->once())
            ->method('getGuid')
            ->willReturn($agreementGuidMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(3))
            ->method('getRecipient')
            ->willReturn($agreementRecipientMock);

        $agreementStatusMock = $this->createMock(AgreementStatus::class);
        $agreementStatusMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(Status::ACTIVE());

        $agreementHistoryMock = $this->createMock(StatusHistory::class);
        $agreementHistoryMock->expects($this->once())
            ->method('getCurrentStatus')
            ->willReturn($agreementStatusMock);

        $agreementMock = $this->createMock(ReferralAgreement::class);
        $agreementMock->expects($this->once())
            ->method('getStatusHistory')
            ->willReturn($agreementHistoryMock);

        $findByGuidMock = $this->createMock(FindByGuid::class);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($findByGuidMock)
            ->willReturn($agreementMock);

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->never())
            ->method('newFindUserByUsername');

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);
        $raMessageFactoryMock->expects($this->once())
            ->method('newFindAgreementByGuid')
            ->with($agreementGuidMock)
            ->willReturn($findByGuidMock);

        $guardEventMock = $this->createMock(GuardEvent::class);
        $guardEventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);
        $guardEventMock->expects($this->never())
            ->method('addTransitionBlocker');

        $guardMock = new GuardAcquireSubscriber($appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $guardMock->confirmCanAcquireReferral($guardEventMock);
    }

    public function testConfirmAcquireReferralDoesNotSetTransitionBlockerWhenContactIsUser()
    {
        $emailAddress = 'test@papalocal.com';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);

        $contactRecipientMock = $this->createMock(ContactRecipient::class);
        $contactRecipientMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(3))
            ->method('getRecipient')
            ->willReturn($contactRecipientMock);

        $findByUsernameMock = $this->createMock(FindUserByUsername::class);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->once())
            ->method('dispatch');

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->once())
            ->method('newFindUserByUsername')
            ->with($emailAddress)
            ->willReturn($findByUsernameMock);

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);
        $raMessageFactoryMock->expects($this->never())
            ->method('newFindAgreementByGuid');

        $guardEventMock = $this->createMock(GuardEvent::class);
        $guardEventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);
        $guardEventMock->expects($this->never())
            ->method('addTransitionBlocker');

        $guardMock = new GuardAcquireSubscriber($appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $guardMock->confirmCanAcquireReferral($guardEventMock);
    }

    public function testConfirmCanAcquireReferralSetsTransitionBlockerWhenContactIsNotUser()
    {
        $emailAddress = 'test@papalocal.com';
        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);

        $contactRecipientMock = $this->createMock(ContactRecipient::class);
        $contactRecipientMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);

        $referralMock = $this->createMock(Referral::class);
        $referralMock->expects($this->exactly(3))
            ->method('getRecipient')
            ->willReturn($contactRecipientMock);

        $findByUsernameMock = $this->createMock(FindUserByUsername::class);

        $appBusMock = $this->createMock(MessageBusInterface::class);
        $appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($findByUsernameMock)
            ->willThrowException(new UserNotFoundException());

        $iaMessageFactoryMock = $this->createMock(\PapaLocal\IdentityAccess\Message\MessageFactory::class);
        $iaMessageFactoryMock->expects($this->once())
            ->method('newFindUserByUsername')
            ->with($emailAddress)
            ->willReturn($findByUsernameMock);

        $raMessageFactoryMock = $this->createMock(MessageFactory::class);
        $raMessageFactoryMock->expects($this->never())
            ->method('newFindAgreementByGuid');

        $guardEventMock = $this->createMock(GuardEvent::class);
        $guardEventMock->expects($this->exactly(3))
            ->method('getSubject')
            ->willReturn($referralMock);
        $guardEventMock->expects($this->once())
            ->method('addTransitionBlocker');

        $guardMock = new GuardAcquireSubscriber($appBusMock, $raMessageFactoryMock, $iaMessageFactoryMock);
        $guardMock->confirmCanAcquireReferral($guardEventMock);
    }
}