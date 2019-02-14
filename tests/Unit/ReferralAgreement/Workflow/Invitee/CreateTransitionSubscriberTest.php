<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/18/18
 */


namespace Test\Unit\ReferralAgreement\Workflow\Invitee;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\Entity\Exception\UserNotFoundException;
use PapaLocal\Entity\User;
use PapaLocal\IdentityAccess\Message\MessageFactory;
use PapaLocal\IdentityAccess\Message\Query\User\FindUserByUsername;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInvitee;
use PapaLocal\ReferralAgreement\Data\MessageFactory as RA_DataMsgFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\Workflow\Invitee\CreateTransitionSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Event\Event;


/**
 * Class CreateTransitionSubscriberTest.
 *
 * @package Test\Unit\ReferralAgreement\Workflow\Invitee
 */
class CreateTransitionSubscriberTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $iaMessageFactoryMock,
        $appBusMock,
        $raDataMsgFactoryMock,
        $mysqlBusMock;

    protected function setUp()
    {
        parent::setUp();

        $this->iaMessageFactoryMock = $this->createMock(MessageFactory::class);
        $this->appBusMock = $this->createMock(MessageBusInterface::class);
        $this->raDataMsgFactoryMock = $this->createMock(RA_DataMsgFactory::class);
        $this->mysqlBusMock = $this->createMock(MessageBusInterface::class);
    }


    public function testSubscriberIsSuccessWhenInviteeIsUser()
    {
        // set up fixtures
        $emailAddress = 'test@papalocal.com';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);

        $userGuidMock = $this->createMock(Guid::class);

        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);
        $inviteeMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);
        $inviteeMock->expects($this->once())
            ->method('setUserId')
            ->with($this->equalTo($userGuidMock))
            ->willReturn($inviteeMock);

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($inviteeMock);

        $findUsrQryMock = $this->createMock(FindUserByUsername::class);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('getGuid')
            ->willReturn($userGuidMock);

        $this->iaMessageFactoryMock->expects($this->once())
            ->method('newFindUserByUsername')
            ->with($this->equalTo($emailAddress))
            ->willReturn($findUsrQryMock);

        $this->appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($findUsrQryMock))
            ->willReturn($userMock);

        $saveInviteeCmdMock = $this->createMock(SaveInvitee::class);

        $this->raDataMsgFactoryMock->expects($this->once())
            ->method('newSaveInvitee')
            ->willReturn($saveInviteeCmdMock);

        $this->mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($saveInviteeCmdMock));

        // exercise SUT
        $subscriber = new CreateTransitionSubscriber($this->iaMessageFactoryMock, $this->appBusMock, $this->raDataMsgFactoryMock, $this->mysqlBusMock);
        $subscriber->createInvitee($eventMock);
    }

    public function testSubscriberIsSuccessWhenInviteeNotUser()
    {
        // set up fixtures
        $emailAddress = 'test@papalocal.com';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);


        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);
        $inviteeMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);
        $inviteeMock->expects($this->never())
            ->method('setUserId');

        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->once())
            ->method('getSubject')
            ->willReturn($inviteeMock);

        $findUsrQryMock = $this->createMock(FindUserByUsername::class);

        $this->iaMessageFactoryMock->expects($this->once())
            ->method('newFindUserByUsername')
            ->with($this->equalTo($emailAddress))
            ->willReturn($findUsrQryMock);

        $this->appBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($findUsrQryMock))
            ->willThrowException(new UserNotFoundException());

        $saveInviteeCmdMock = $this->createMock(SaveInvitee::class);

        $this->raDataMsgFactoryMock->expects($this->once())
            ->method('newSaveInvitee')
            ->willReturn($saveInviteeCmdMock);

        $this->mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($saveInviteeCmdMock));

        // exercise SUT
        $subscriber = new CreateTransitionSubscriber($this->iaMessageFactoryMock, $this->appBusMock, $this->raDataMsgFactoryMock, $this->mysqlBusMock);
        $subscriber->createInvitee($eventMock);
    }
}