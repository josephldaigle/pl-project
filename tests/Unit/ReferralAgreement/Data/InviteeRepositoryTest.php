<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 7:36 PM
 */

namespace Test\Unit\ReferralAgreement\Data;


use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\Query\FindByGuid;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Data\Query\Invitee\FindOne;
use PapaLocal\ReferralAgreement\Entity\Factory\InviteeFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\ValueObject\Invitee\InviteeIdentifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;


/**
 * Class InviteeRepositoryTest
 *
 * @package Test\Unit\ReferralAgreement\Data
 */
class InviteeRepositoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $mysqlBusMock;

    /**
     * @var MockObject
     */
    private $msgFactoryMock;

    /**
     * @var MockObject
     */
    private $inviteeFacMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->mysqlBusMock = $this->createMock(MessageBusInterface::class);
        $this->msgFactoryMock = $this->createMock(MessageFactory::class);
        $this->inviteeFacMock = $this->createMock(InviteeFactory::class);

        parent::setUp();
    }

    public function testFindByGuidIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $queryMock = $this->createMock(FindByGuid::class);
        $recordMock = $this->createMock(RecordInterface::class);
        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);

        $this->msgFactoryMock->expects($this->once())
            ->method('newFindByGuid')
            ->with($this->equalTo('v_referral_agreement_invitee'), $this->equalTo($guidMock))
            ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($queryMock))
            ->willReturn($recordMock);

        $this->inviteeFacMock->expects($this->once())
            ->method('createFromRecord')
            ->with($this->equalTo($recordMock))
            ->willReturn($inviteeMock);

        // exercise SUT
        $repository = new InviteeRepository($this->mysqlBusMock, $this->msgFactoryMock, $this->inviteeFacMock);

        $result = $repository->findByGuid($guidMock);

        $this->assertSame($inviteeMock, $result);
    }

    public function testFindAllByAgreementGuidIsSuccess()
    {
        // set up fixtures
        $agmtGuid = 'ce807c52-1427-4ab6-97d8-c133db787dd5';

        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->once())
            ->method('value')
            ->willReturn($agmtGuid);

        $queryMock = $this->createMock(FindBy::class);
        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $collectionMock = $this->createMock(Collection::class);

        $this->msgFactoryMock->expects($this->once())
                             ->method('newFindBy')
                             ->with($this->equalTo('v_referral_agreement_invitee'), $this->equalTo('agreementGuid'), $this->equalTo($agmtGuid))
                             ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        $this->inviteeFacMock->expects($this->once())
                             ->method('createFromRecordSet')
                             ->with($this->equalTo($recordSetMock))
                             ->willReturn($collectionMock);

        // exercise SUT
        $repository = new InviteeRepository($this->mysqlBusMock, $this->msgFactoryMock, $this->inviteeFacMock);

        $result = $repository->findAllByAgreementGuid($guidMock);

        $this->assertSame($collectionMock, $result);
    }

    public function testFindAllByEmailAddressIsSuccess()
    {
        // set up fixtures
        $email = 'test@papalocal.com';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($email);

        $queryMock = $this->createMock(FindBy::class);
        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $collectionMock = $this->createMock(Collection::class);

        $this->msgFactoryMock->expects($this->once())
                             ->method('newFindBy')
                             ->with($this->equalTo('v_referral_agreement_invitee'), $this->equalTo('emailAddress'), $this->equalTo($email))
                             ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        $this->inviteeFacMock->expects($this->once())
                             ->method('createFromRecordSet')
                             ->with($this->equalTo($recordSetMock))
                             ->willReturn($collectionMock);

        // exercise SUT
        $repository = new InviteeRepository($this->mysqlBusMock, $this->msgFactoryMock, $this->inviteeFacMock);

        $result = $repository->findAllByEmailAddress($emailAddressMock);

        $this->assertSame($collectionMock, $result);
    }

    public function testFindAllByUserGuidIsSuccess()
    {
        // set up fixtures
        $userGuid = 'ce807c52-1427-4ab6-97d8-c133db787dd5';

        $guidMock = $this->createMock(GuidInterface::class);
        $guidMock->expects($this->once())
                 ->method('value')
                 ->willReturn($userGuid);

        $queryMock = $this->createMock(FindBy::class);
        $recordSetMock = $this->createMock(RecordSetInterface::class);
        $collectionMock = $this->createMock(Collection::class);

        $this->msgFactoryMock->expects($this->once())
                             ->method('newFindBy')
                             ->with($this->equalTo('v_referral_agreement_invitee'), $this->equalTo('userGuid'), $this->equalTo($userGuid))
                             ->willReturn($queryMock);

        $this->mysqlBusMock->expects($this->once())
                           ->method('dispatch')
                           ->with($this->equalTo($queryMock))
                           ->willReturn($recordSetMock);

        $this->inviteeFacMock->expects($this->once())
                             ->method('createFromRecordSet')
                             ->with($this->equalTo($recordSetMock))
                             ->willReturn($collectionMock);

        // exercise SUT
        $repository = new InviteeRepository($this->mysqlBusMock, $this->msgFactoryMock, $this->inviteeFacMock);

        $result = $repository->findAllByUserGuid($guidMock);

        $this->assertSame($collectionMock, $result);
    }
}