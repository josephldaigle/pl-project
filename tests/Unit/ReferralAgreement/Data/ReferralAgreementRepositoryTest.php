<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/30/18
 * Time: 5:27 PM
 */


namespace Test\Unit\ReferralAgreement\Data;


use PapaLocal\Referral\Message\MessageFactory as ReferralMessageFactory;
use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\Query\FindByCols;
use PapaLocal\Core\Data\Query\FindByGuid;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\Core\ValueObject\Collection\ListBuilder;
use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Entity\Factory\ReferralAgreementFactory;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\MessageBus;
use PHPUnit\Framework\TestCase;


/**
 * Class ReferralAgreementRepositoryTest
 *
 * @package Test\Unit\ReferralAgreement\Data
 */
class ReferralAgreementRepositoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $messageBusMock,
        $messageFactoryMock,
        $agmtFactoryMock,
        $guidFactoryMock,
        $appBusMock,
        $referralMsgFactoryMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        // configure mocks
        $this->messageBusMock = $this->createMock(MessageBus::class);
        $this->messageFactoryMock = $this->createMock(MessageFactory::class);
        $this->agmtFactoryMock = $this->createMock(ReferralAgreementFactory::class);
        $this->guidFactoryMock = $this->createMock(GuidFactory::class);
        $this->appBusMock = $this->createMock(MessageBus::class);
        $this->referralMsgFactoryMock = $this->createMock(ReferralMessageFactory::class);
    }

    public function testFindByGuidIsSuccess()
    {
        // set up fixtures
        $agmtGuidMock = $this->createMock(GuidInterface::class);
        $agmtGuid = 'd18ec8da-39a1-4386-abc9-e9c7d6fea186';
        $agmtId = 5;

        // configure query results
        $headerRecMock = $this->createMock(RecordInterface::class);
        $headerRecMock->expects($this->exactly(3))
            ->method('offsetGet')
            ->withConsecutive([$this->equalTo('id')], [$this->equalTo('id')], [$this->equalTo('guid')])
            ->willReturnOnConsecutiveCalls($agmtId, $agmtId, $agmtGuid);

        $recSetMock = $this->createMock(RecordSetInterface::class);

        $findByGuidQryMock = $this->createMock(FindByGuid::class);

        $findByQryMock = $this->createMock(FindBy::class);

        // configure msg factory
        $this->messageFactoryMock->expects($this->once())
            ->method('newFindByGuid')
            ->with($this->equalTo('v_referral_agreement'), $this->equalTo($agmtGuidMock))
            ->willReturn($findByGuidQryMock);
        $this->messageFactoryMock->expects($this->exactly(3))
            ->method('newFindBy')
            ->withConsecutive(
                [$this->equalTo('v_referral_agreement_location'), $this->equalTo('agreementId'), $this->equalTo($agmtId)],
                [$this->equalTo('v_referral_agreement_service'), $this->equalTo('agreementId'), $this->equalTo($agmtId)],
                [$this->equalTo('v_referral_agreement_status_history'), $this->equalTo('agreementGuid'), $this->equalTo($agmtGuid)]
            )
            ->willReturn($findByQryMock);

        $repository = new ReferralAgreementRepository($this->messageBusMock, $this->messageFactoryMock, $this->agmtFactoryMock, $this->guidFactoryMock, $this->appBusMock, $this->referralMsgFactoryMock);

        // configure msg bus
        $this->messageBusMock->expects($this->exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo($findByGuidQryMock)],
                [$this->equalTo($findByQryMock)],
                [$this->equalTo($findByQryMock)],
                [$this->equalTo($findByQryMock)]
            )
            ->willReturnOnConsecutiveCalls($headerRecMock, $recSetMock, $recSetMock, $recSetMock);

        // configure agreement factory
        $refAgmtMock = $this->createMock(ReferralAgreement::class);
        
        $this->agmtFactoryMock->expects($this->once())
            ->method('createFromRecords')
            ->with($this->equalTo($headerRecMock), $this->equalTo($recSetMock), $this->equalTo($recSetMock), $this->equalTo($recSetMock))
            ->willReturn($refAgmtMock);

        // exercise SUT
        $result = $repository->findByGuid($agmtGuidMock);

        // make assertions
        $this->assertEquals($refAgmtMock, $result, 'unexpected result');
    }


    public function testLoadUserAgreementsIsSuccess()
    {
        // set up fixtures
        $userGuid = '2999e13a-25ab-4792-8454-e9c4a7b276a9';
        $userGuidMock = $this->createMock(GuidInterface::class);
        $userGuidMock->expects($this->once())
            ->method('value')
            ->willReturn($userGuid);

        $agmtGuid = '660c111c-cf5f-4732-b7f7-3693c6a74d8f';

        $agmtGuidMock = $this->createMock(GuidInterface::class);

        $this->guidFactoryMock->expects($this->once())
            ->method('createFromString')
            ->with($this->equalTo($agmtGuid))
            ->willReturn($agmtGuidMock);

        $agmtHdrRecMock = $this->createMock(RecordInterface::class);
        $agmtHdrRecMock->expects($this->once())
            ->method('offsetGet')
            ->with($this->equalTo('guid'))
            ->willReturn($agmtGuid);

        $resultCollection = $this->createMock(Collection::class);

        $refAgmtMock = $this->createMock(ReferralAgreement::class);

        $headerMock = $this->createMock(RecordSetInterface::class);
        $headerMock->expects($this->exactly(2))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, false);
        $headerMock->expects($this->once())
            ->method('current')
            ->willReturn($agmtHdrRecMock);

        $findByQryMock = $this->createMock(FindBy::class);

        $this->messageFactoryMock->expects($this->once())
            ->method('newFindBy')
            ->with($this->equalTo('v_referral_agreement'), $this->equalTo('ownerGuid'), $this->equalTo($userGuid))
            ->willReturn($findByQryMock);

        $this->messageBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($findByQryMock))
            ->willReturn($headerMock);

        $agmtListBldrMock = $this->createMock(ListBuilder::class);
        $agmtListBldrMock->expects($this->once())
            ->method('add')
            ->with($this->equalTo($refAgmtMock))
            ->willReturn($agmtListBldrMock);
        $agmtListBldrMock->expects($this->once())
            ->method('build')
            ->willReturn($resultCollection);

        $this->agmtFactoryMock->expects($this->once())
            ->method('getListBuilder')
            ->willReturn($agmtListBldrMock);

        $repository = $this->getMockBuilder(ReferralAgreementRepository::class)
            ->setConstructorArgs([$this->messageBusMock, $this->messageFactoryMock, $this->agmtFactoryMock, $this->guidFactoryMock, $this->appBusMock, $this->referralMsgFactoryMock])
            ->setMethodsExcept(['loadUserAgreements'])
            ->getMock();

        $repository->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($agmtGuidMock))
            ->willReturn($refAgmtMock);

        // exercise SUT
        $result = $repository->loadUserAgreements($userGuidMock);

        // make assertions
        $this->assertSame($resultCollection, $result, 'unexpected type');
    }

    /**
     * TODO: Refactor
     */
    public function testLoadInviteeAgreementsIsSuccess()
    {
        $this->markTestIncomplete();
        // set up fixtures
        $userGuid = '2999e13a-25ab-4792-8454-e9c4a7b276a9';
        $userGuidMock = $this->createMock(GuidInterface::class);
        $userGuidMock->expects($this->once())
                     ->method('value')
                     ->willReturn($userGuid);

        $agmtGuid = '660c111c-cf5f-4732-b7f7-3693c6a74d8f';

        $agmtGuidMock = $this->createMock(GuidInterface::class);

        $this->guidFactoryMock->expects($this->once())
                              ->method('createFromString')
                              ->with($this->equalTo($agmtGuid))
                              ->willReturn($agmtGuidMock);

        $agmtHdrRecMock = $this->createMock(RecordInterface::class);
        $agmtHdrRecMock->expects($this->once())
                       ->method('offsetGet')
                       ->with($this->equalTo('agreementGuid'))
                       ->willReturn($agmtGuid);

        $resultCollection = $this->createMock(Collection::class);

        $refAgmtMock = $this->createMock(ReferralAgreement::class);

        $headerSetMock = $this->createMock(RecordSetInterface::class);
        $headerSetMock->expects($this->exactly(2))
                   ->method('valid')
                   ->willReturnOnConsecutiveCalls(true, false);
        $headerSetMock->expects($this->once())
                   ->method('current')
                   ->willReturn($agmtHdrRecMock);

        $findByQryMock = $this->createMock(FindByCols::class);

        $this->messageFactoryMock->expects($this->once())
                                 ->method('newFindByCols')
                                 ->with($this->equalTo('v_referral_agreement_invitee'),
                                     $this->equalTo(['userGuid' => $userGuid, 'sent' => 1, 'isParticipant' => 0, 'declined' => 0]))
                                 ->willReturn($findByQryMock);

        $this->messageBusMock->expects($this->once())
                             ->method('dispatch')
                             ->with($this->equalTo($findByQryMock))
                             ->willReturn($headerSetMock);

        $agmtListBldrMock = $this->createMock(ListBuilder::class);
        $agmtListBldrMock->expects($this->once())
                         ->method('add')
                         ->with($this->equalTo($refAgmtMock))
                         ->willReturn($agmtListBldrMock);
        $agmtListBldrMock->expects($this->once())
                         ->method('build')
                         ->willReturn($resultCollection);

        $this->agmtFactoryMock->expects($this->once())
                              ->method('getListBuilder')
                              ->willReturn($agmtListBldrMock);

        $this->guidFactoryMock->expects($this->once())
            ->method('createFromString')
            ->with($this->equalTo($agmtGuid))
            ->willReturn($agmtGuidMock);

        $repository = $this->getMockBuilder(ReferralAgreementRepository::class)
                           ->setConstructorArgs([$this->messageBusMock, $this->messageFactoryMock, $this->agmtFactoryMock, $this->guidFactoryMock, $this->appBusMock, $this->referralMsgFactoryMock])
                           ->setMethodsExcept(['loadInviteeAgreements'])
                           ->getMock();

        $repository->expects($this->once())
                   ->method('findByGuid')
                   ->with($this->equalTo($agmtGuidMock))
                   ->willReturn($refAgmtMock);

        // exercise SUT
        $result = $repository->loadInviteeAgreements($userGuidMock);

        // make assertions
        $this->assertSame($resultCollection, $result, 'unexpected type');
    }
}