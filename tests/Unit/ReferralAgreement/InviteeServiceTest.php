<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 12/1/18
 * Time: 1:21 PM
 */

namespace Test\Unit\ReferralAgreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\MarkInvitationSent;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Data\MessageFactory;
use PapaLocal\ReferralAgreement\InviteeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\Registry;


/**
 * Class InviteeServiceTest
 *
 * @package Test\Unit\ReferralAgreement
 */
class InviteeServiceTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $inviteeRepositoryMock,
        $workflowRegistryMock,
        $mysqlBusMock,
        $mysqlMsgFactoryMock;

    /**
     * @var InviteeService
     */
    private $inviteeService;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->inviteeRepositoryMock = $this->createMock(InviteeRepository::class);
        $this->workflowRegistryMock = $this->createMock(Registry::class);
        $this->mysqlBusMock = $this->createMock(MessageBusInterface::class);
        $this->mysqlMsgFactoryMock = $this->createMock(MessageFactory::class);

        $this->inviteeService = new InviteeService($this->inviteeRepositoryMock, $this->workflowRegistryMock, $this->mysqlBusMock, $this->mysqlMsgFactoryMock);
    }


    public function testMarkInvitationAsSentIsSuccess()
    {
        // set up fixtures
        $invitationGuidMock = $this->createMock(GuidInterface::class);
        $markSentCmdMock = $this->createMock(MarkInvitationSent::class);

        $this->mysqlMsgFactoryMock->expects($this->once())
            ->method('newMarkInvitationSent')
            ->with($this->equalTo($invitationGuidMock))
            ->willReturn($markSentCmdMock);

        $this->mysqlBusMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($markSentCmdMock));

        // exercise SUT
        $this->inviteeService->markInvitationAsSent($invitationGuidMock);
    }
}