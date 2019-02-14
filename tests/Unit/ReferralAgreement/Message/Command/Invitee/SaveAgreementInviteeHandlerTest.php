<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/17/18
 * Time: 8:48 PM
 */


namespace Test\Unit\ReferralAgreement\Message\Command\Invitee;

use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Core\ValueObject\EmailAddressType;
use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreementInvitee;
use PapaLocal\ReferralAgreement\InviteeService;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\SaveAgreementInvitee;
use PapaLocal\ReferralAgreement\Message\Command\Invitee\SaveAgreementInviteeHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class SaveAgreementInviteeHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Command\Invitee
 */
class SaveAgreementInviteeHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $inviteeGuid = 'd995e67e-8276-4c0f-9451-b27b55523656';
        $agreementGuid = '5675b0b6-e078-4194-978c-8b0e9cc02dedf';

        $guidMock = $this->createMock(Guid::class);
        $guidMock->expects($this->exactly(2))
            ->method('value')
            ->willReturnOnConsecutiveCalls($inviteeGuid, $agreementGuid);

        $firstName = 'Guy';
        $lastName = 'Tester';
        $message = 'Guy, sell me test referrals.';
        $emailAddress = 'guy@papalocal.com';

        $emailAddressMock = $this->createMock(EmailAddress::class);
        $emailAddressMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailAddress);
        $emailAddressMock->expects($this->once())
            ->method('getType')
            ->willReturn(EmailAddressType::PERSONAL());

        $inviteeMock = $this->createMock(ReferralAgreementInvitee::class);
        
        $inviteeServiceMock = $this->createMock(InviteeService::class);
        $inviteeServiceMock->expects($this->once())
            ->method('saveInvitee')
            ->with($this->equalTo($inviteeMock));
        
        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->once())
            ->method('denormalize')
            ->with($this->equalTo([
                'guid' => array('value' => $inviteeGuid),
                'agreementId' => array('value' => $agreementGuid),
                'firstName' => $firstName,
                'lastName' => $lastName,
                'message' => $message,
                'emailAddress' => array('emailAddress' => $emailAddress, 'type' => array('value' => EmailAddressType::PERSONAL()->getValue()))
            ]))
            ->willReturn($inviteeMock);

        $commandMock = $this->createMock(SaveAgreementInvitee::class);
        $commandMock->expects($this->once())
            ->method('getInviteeGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($guidMock);
        $commandMock->expects($this->once())
            ->method('getFirstName')
            ->willReturn($firstName);
        $commandMock->expects($this->once())
            ->method('getLastName')
            ->willReturn($lastName);
        $commandMock->expects($this->once())
            ->method('getMessage')
            ->willReturn($message);
        $commandMock->expects($this->exactly(2))
            ->method('getEmailAddress')
            ->willReturn($emailAddressMock);

        // exercise SUT
        $handler = new SaveAgreementInviteeHandler($inviteeServiceMock, $serializerMock);
        $handler->__invoke($commandMock);
    }
}