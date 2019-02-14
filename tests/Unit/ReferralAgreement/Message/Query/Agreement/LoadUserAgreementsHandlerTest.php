<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 10:17 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\Entity\Collection\Collection;
use PapaLocal\ReferralAgreement\Data\ReferralAgreementRepository;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadUserAgreements;
use PHPUnit\Framework\TestCase;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadUserAgreementsHandler;


/**
 * Class LoadUserAgreementsHandlerTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Agreement
 */
class LoadUserAgreementsHandlerTest extends TestCase
{
    public function testCanInstantiate()
    {
        // set up fixtures
        $repoMock = $this->createMock(ReferralAgreementRepository::class);

        // exercise SUT
        $handler = new LoadUserAgreementsHandler($repoMock);

        // make assertions
        $this->assertInstanceOf(LoadUserAgreementsHandler::class, $handler);
    }

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        $queryMock = $this->createMock(LoadUserAgreements::class);
        $queryMock->expects($this->once())
            ->method('getOwnerGuid')
            ->willReturn($guidMock);

        $collectionMock = $this->createMock(Collection::class);

        $repoMock = $this->createMock(ReferralAgreementRepository::class);
        $repoMock->expects($this->once())
            ->method('loadUserAgreements')
            ->willReturn($collectionMock);

        // exercise SUT
        $handler = new LoadUserAgreementsHandler($repoMock);
        $handler->__invoke($queryMock);

        // make assertions
        $this->assertInstanceOf(LoadUserAgreementsHandler::class, $handler);
    }
}