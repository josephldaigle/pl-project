<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:21 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Invitee;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByUserGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByUserGuidHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByUserGuidHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Invitee
 */
class FindByUserGuidHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $queryMock = $this->createMock(FindByUserGuid::class);
        $queryMock->expects($this->once())
            ->method('getUserGuid')
            ->willReturn($guidMock);

        $collectionMock = $this->createMock(Collection::class);

        $inviteeRepoMock = $this->createMock(InviteeRepository::class);
        $inviteeRepoMock->expects($this->once())
            ->method('findAllByUserGuid')
            ->with($this->equalTo($guidMock))
            ->willReturn($collectionMock);

        // exercise SUT
        $handler = new FindByUserGuidHandler($inviteeRepoMock);
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($collectionMock, $result);
    }
}