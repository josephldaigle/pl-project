<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/12/18
 * Time: 1:45 PM
 */

namespace Test\Integration\ReferralAgreement\Workflow\Invitee;


use PapaLocal\Core\Notification\Emailer;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Notification\Notifier;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Entity\Owner;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\Workflow\Invitee\InviteTransitionSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class InviteTransitionSubscriberTest
 *
 * @package Test\Integration\ReferralAgreement\Workflow\Invitee
 */
class InviteTransitionSubscriberTest extends KernelTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // boot kernel
        self::bootKernel();
    }


    public function testCanNotifyUserInvitee()
    {
        $this->markTestIncomplete();

        // set up fixtures
        $notifierMock = $this->getMockBuilder(Notifier::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['sendUserNotification'])
            ->getMock();

        $notificationFactory = self::$container->get('PapaLocal\ReferralAgreement\Notification\NotificationFactory');

        $emailerMock = $this->createMock(Emailer::class);

        $ownerMock = $this->createMock(Owner::class);

        $referralAgmtMock = $this->createMock(ReferralAgreement::class);
        $referralAgmtMock->expects($this->exactly(2))
                         ->method('getOwner')
                         ->willReturn($ownerMock);
        $referralAgmtMock->expects($this->once())
                         ->method('getBid')
                         ->willReturn(15.00);


        $referralAgmtRepoMock = $this->createMock(ReferralAgreementRepository::class);
        $referralAgmtRepoMock->expects($this->once())
            ->method('loadAgreementDetail')
            ->willReturn($referralAgmtMock);

        $inviteeRepoMock = $this->createMock(InviteeRepository::class);
        $inviteeRepoMock->expects($this->once())
            ->method('markAsSent');

        $agmtIdMock = $this->createMock(Guid::class);

        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);
        $inviteeMock->expects($this->once())
            ->method('getAgreementId')
            ->willReturn($agmtIdMock);
        $inviteeMock->expects($this->once())
            ->method('isUser')
            ->willReturn(true);
        $event = $this->createMock(Event::class);
        $event->expects($this->once())
            ->method('getSubject')
            ->willReturn($inviteeMock);

        $subscriber = new InviteTransitionSubscriber($notifierMock, $notificationFactory, $emailerMock, $referralAgmtRepoMock, $inviteeRepoMock);
        // exercise SUT
        $subscriber->inviteTransition($event);

        // make assertions
    }

    public function testCanNotifyNonUserInvitee()
    {
        $this->markTestIncomplete();
    }

    public function testCanMarkNotificationAsSent()
    {
        $this->markTestIncomplete();
    }
}