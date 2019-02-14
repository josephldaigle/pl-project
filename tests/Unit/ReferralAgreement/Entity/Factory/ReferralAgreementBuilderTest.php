<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 6:41 PM
 */

namespace Test\Unit\ReferralAgreement\Entity\Factory\Builder;


use PapaLocal\Core\ValueObject\Guid;
use PapaLocal\ReferralAgreement\Entity\Factory\ReferralAgreementBuilder;
use PapaLocal\ReferralAgreement\Entity\ReferralAgreement;
use PHPUnit\Framework\TestCase;


/**
 * Class ReferralAgreementBuilderTest.
 *
 * @package Test\Unit\ReferralAgreement\Entity\Factory\Builder
 */
class ReferralAgreementBuilderTest extends TestCase
{
    public function testCanBuildWithMinDataSet()
    {
        // set up fixtures
        $agmtGuidMock = $this->createMock(Guid::class);

        $coGuidMock = $this->createMock(Guid::class);

        $name = 'A Test Agreement';
        $description = 'This is a test agreement.';
        $quantity = 10;
        $strategy = 'weekly';
        $bid = 15.00;

        $builder = new ReferralAgreementBuilder();

        $agreement = $builder->setGuid($agmtGuidMock)
            ->setCompanyGuid($coGuidMock)
            ->setName($name)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setStrategy($strategy)
            ->setBid($bid)
            ->build();

        $this->assertInstanceOf(ReferralAgreement::class, $agreement, 'unexpected type');
        $this->assertEquals($agmtGuidMock, $agreement->getGuid(), 'unexpected agreement guid');
        $this->assertEquals($coGuidMock, $agreement->getCompanyGuid(), 'unexpected company guid');
        $this->assertEquals($name, $agreement->getName(), 'unexpected name');
        $this->assertEquals($description, $agreement->getDescription(), 'unexpected description');
        $this->assertEquals($strategy, $agreement->getStrategy(), 'unexpected strategy');
        $this->assertEquals($bid, $agreement->getBid(), 'unexpected bid');
    }
}