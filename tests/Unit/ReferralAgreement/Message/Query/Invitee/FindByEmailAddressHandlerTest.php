<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:16 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\Core\ValueObject\EmailAddress;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByEmailAddress;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByEmailAddressHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByEmailAddressHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Invitee
 */
class FindByEmailAddressHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $emailMock = $this->createMock(EmailAddress::class);
        $queryMock = $this->createMock(FindByEmailAddress::class);
        $queryMock->expects($this->once())
            ->method('getEmailAddress')
            ->willReturn($emailMock);
        $collectionMock = $this->createMock(Collection::class);

        $inviteeRepoMock = $this->createMock(InviteeRepository::class);
        $inviteeRepoMock->expects($this->once())
            ->method('findAllByEmailAddress')
            ->with($this->equalTo($emailMock))
            ->willReturn($collectionMock);

        // exercise SUT
        $handler = new FindByEmailAddressHandler($inviteeRepoMock);
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($collectionMock, $result);
    }
}