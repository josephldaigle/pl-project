<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/20/18
 * Time: 10:43 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Invitee;

use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\InviteeRepository;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByAgreementGuid;
use PapaLocal\ReferralAgreement\Message\Query\Invitee\FindByAgreementGuidHandler;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByAgreementGuidHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Invitee
 */
class FindByAgreementGuidHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);
        $collectionMock = $this->createMock(Collection::class);
        
        $queryMock = $this->createMock(FindByAgreementGuid::class);
        $queryMock->expects($this->once())
            ->method('getAgreementGuid')
            ->willReturn($guidMock);
        
        $inviteeRepoMock = $this->createMock(InviteeRepository::class);
        $inviteeRepoMock->expects($this->once())
            ->method('findAllByAgreementGuid')
            ->with($this->equalTo($guidMock))
            ->willReturn($collectionMock);

        // exercise SUT
        $handler = new FindByAgreementGuidHandler($inviteeRepoMock);
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($collectionMock, $result);
    }

}