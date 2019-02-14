<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 9:57 PM
 */

namespace Test\Unit\ReferralAgreement\Message\Query\Agreement;


use PapaLocal\Core\ValueObject\GuidInterface;
use PapaLocal\ReferralAgreement\Message\Query\Agreement\LoadUserAgreements;
use PHPUnit\Framework\TestCase;


/**
 * Class LoadUserAgreementsTest
 *
 * @package Test\Unit\ReferralAgreement\Message\Query\Agreement
 */
class LoadUserAgreementsTest extends TestCase
{
    public function testCanInstantiate()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = new LoadUserAgreements($guidMock);

        // make assertions
        $this->assertInstanceOf(LoadUserAgreements::class, $query);
    }

    public function testCanFetchOwnerGuidFromQuery()
    {
        // set up fixtures
        $guidMock = $this->createMock(GuidInterface::class);

        // exercise SUT
        $query = new LoadUserAgreements($guidMock);

        // make assertions
        $this->assertEquals($guidMock, $query->getOwnerGuid());
    }
}