<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/11/18
 * Time: 3:02 PM
 */


namespace Test\Unit\ReferralAgreement\Workflow\Agreement;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Notification\NotificationInterface;
use PapaLocal\ReferralAgreement\Notification\AgreementStatusChanged;
use PapaLocal\ReferralAgreement\Notification\NotificationFactory;
use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\ValueObject\AgreementStatus;
use PapaLocal\ReferralAgreement\ValueObject\StatusHistory;
use PapaLocal\ReferralAgreement\Workflow\Agreement\EnteredActiveSubscriber;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;


/**
 * Class EnteredActiveSubscriberTest
 *
 * @package Test\Unit\ReferralAgreement\Workflow\Agreement
 */
class EnteredActiveSubscriberTest extends TestCase
{
    private $workflowRegistryMock;
    private $notifierMock;
    private $notificationFactoryMock;
    private $loggerMock;

    protected function setUp()
    {
        parent::setUp();

        $this->workflowRegistryMock = $this->createMock(Registry::class);
        $this->notifierMock = $this->createMock(Notifier::class);
        $this->notificationFactoryMock = $this->createMock(NotificationFactory::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }


    public function testEnteredActiveAppliesWorkflowTransitionWhenInviteeIsNotParticipant()
    {
        // set up fixtures
        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);
        $inviteeMock->expects($this->once())
            ->method('getCurrentPlace')
            ->willReturn('Created');

        $workflowMock = $this->createMock(Workflow::class);
        $workflowMock->expects($this->once())
            ->method('apply')
            ->with($inviteeMock, 'invite');

        $this->workflowRegistryMock->expects($this->once())
            ->method('get')
            ->willReturn($workflowMock);

        $agreementMock = $this->createMock(ReferralAgreement::class);
        $agreementMock->expects($this->once())
            ->method('getInvitees')
            ->willReturn(array($inviteeMock));


        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($agreementMock);

        $subscriber = new EnteredActiveSubscriber($this->workflowRegistryMock, $this->notifierMock, $this->notificationFactoryMock, $this->loggerMock);

        // exercise SUT
        $subscriber->enteredActive($eventMock);
    }

    public function testEnteredActiveSendsAppNotificationWhenInviteeIsParticipant()
    {
        // set up fixtures
        $emailMock = $this->createMock(EmailAddress::class);
        $emailMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn('test@papalocal.com');

        $guidMock = $this->createMock(GuidInterface::class);

        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);

        $inviteeMock->expects($this->once())
                    ->method('getCurrentPlace')
                    ->willReturn('Invited');
        $inviteeMock->expects($this->once())
                    ->method('isParticipant')
                    ->willReturn(true);
        $inviteeMock->expects($this->once())
                    ->method('getUserId')
                    ->willReturn($guidMock);
        $inviteeMock->expects($this->once())
                    ->method('getEmailAddress')
                    ->willReturn($emailMock);

        $this->workflowRegistryMock->expects($this->never())
                                   ->method('get');

        $statusMock = $this->createMock(AgreementStatus::class);

        $statusHistoryMock = $this->createMock(StatusHistory::class);
        $statusHistoryMock->expects($this->once())
            ->method('getCurrentStatus')
            ->willReturn($statusMock);

        $agreementMock = $this->createMock(ReferralAgreement::class);
        $agreementMock->expects($this->once())
                      ->method('getInvitees')
                      ->willReturn(array($inviteeMock));
        $agreementMock->expects($this->once())
            ->method('getName')
            ->willReturn('Test Agreement Name');
        $agreementMock->expects($this->once())
            ->method('getStatusHistory')
            ->willReturn($statusHistoryMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
                  ->method('getSubject')
                  ->willReturn($agreementMock);

        $notificationMock = $this->createMock(AgreementStatusChanged::class);

        $this->notificationFactoryMock->expects($this->once())
            ->method('newAgreementStatusChanged')
            ->willReturn($notificationMock);

        $this->notifierMock->expects($this->once())
            ->method('sendUserNotification');

        $subscriber = new EnteredActiveSubscriber($this->workflowRegistryMock, $this->notifierMock, $this->notificationFactoryMock, $this->loggerMock);

        // exercise SUT
        $subscriber->enteredActive($eventMock);
    }
}