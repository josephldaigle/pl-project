<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 4:16 PM
 */

namespace Test\Unit\ReferralAgreement\Data\Command\Invitee;


use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInvitee;
use PapaLocal\ReferralAgreement\Data\Command\Invitee\SaveInviteeHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class SaveInviteeHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Data\Command\Invitee
 */
class SaveInviteeHandlerTest extends TestCase
{
    public function testCanInstantiate()
    {
        // set up fixtures
        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);

        // exercise SUT
        $handler = new SaveInviteeHandler($tableGatewayMock);

        // make assertions
        $this->assertInstanceOf(SaveInviteeHandler::class, $handler);
    }

    /**
     * @expectedException PapaLocal\Core\Data\Exception\CommandException
     * @expectedExceptionCode 100
     */
    public function testHandlerThrowsExceptionWhenAgreementIdNotFoundInDb()
    {
        // set up fixtures
        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $commandMock = $this->createMock(SaveInvitee::class);
        $commandMock->expects($this->exactly(2))
            ->method('getAgreementGuid')
            ->willReturn('');

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo('v_referral_agreement'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo(''))
            ->willReturn($agmtRecMock);

        // exercise SUT
        $handler = new SaveInviteeHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }

    public function testSaveInviteeHandlerIsSuccess()
    {
        // set up fixtures
        $inviteeGuid = 'ffcbfb1e-fd4d-4c67-814e-01202e1cd4f3';
        $agreementId = 3;
        $agreementGuid = '3f27acab-8e1b-4315-8fb8-7e4409f3217f';
        $firstName = 'Guy';
        $lastName = 'Tester';
        $message = 'Sell me referrals.';
        $emailAddress = 'gtester@papalocal.com';
        $phoneNumber = '5554447777';
        $userGuid = '3d769a09-900f-4ca9-9ab8-68fd1f48a599';

        $agmtRecMock = $this->createMock(RecordInterface::class);
        $agmtRecMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);
        $agmtRecMock->expects($this->once())
            ->method('offsetGet')
            ->with('id')
            ->willReturn($agreementId);

        $commandMock = $this->createMock(SaveInvitee::class);
        $commandMock->expects($this->once())
            ->method('getInviteeGuid')
            ->willReturn($inviteeGuid);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($agreementGuid);
        $commandMock->expects($this->once())
            ->method('getFirstName')
            ->willReturn($firstName);
        $commandMock->expects($this->once())
            ->method('getLastName')
            ->willReturn($lastName);
        $commandMock->expects($this->once())
            ->method('getMessage')
            ->willReturn($message);
        $commandMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);
        $commandMock->expects($this->once())
            ->method('getPhoneNumber')
            ->willReturn($phoneNumber);
        $commandMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($userGuid);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->exactly(2))
            ->method('setTable')
            ->withConsecutive($this->equalTo('v_referral_agreement'),
                $this->equalTo('ReferralAgreementInvitee'));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($agreementGuid))
            ->willReturn($agmtRecMock);
        $tableGatewayMock->expects($this->once())
            ->method('create')
            ->with(array(
                'guid' => $inviteeGuid,
                'agreementId' => $agreementId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'message' => $message,
                'emailAddress' => $emailAddress,
                'phoneNumber' => $phoneNumber,
                'userGuid' => $userGuid,
            ));

        // exercise SUT
        $handler = new SaveInviteeHandler($tableGatewayMock);
        $handler->__invoke($commandMock);
    }
}