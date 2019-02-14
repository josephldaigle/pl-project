<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/6/18
 * Time: 9:16 PM
 */

namespace Test\Unit\ReferralAgreement\ValueObject\Factory;


use PapaLocal\Core\ValueObject\Collection\ListBuilder;
use PapaLocal\Entity\Collection\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;


/**
 * Class ListBuilderTest
 *
 * @package Test\Unit\ReferralAgreement\ValueObject\Factory
 */
class ListBuilderTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $serializerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->serializerMock = $this->createmock(Serializer::class);
    }

    public function testCanInstantiate()
    {
        // set up fixtures
        $listBuilder = new ListBuilder($this->serializerMock);

        // make assertions
        $this->assertInstanceOf(ListBuilder::class, $listBuilder);
    }

    public function testCanBuildList()
    {
        // set up fixtures
        $listMock = $this->createMock(Collection::class);

        $this->serializerMock->expects($this->once())
            ->method('denormalize')
            ->willReturn($listMock);

        $itemMock = $this->createMock(\stdClass::class);

        $listBuilder = new ListBuilder($this->serializerMock);

        // exercise SUT
        $listBuilder->add($itemMock);
        $result = $listBuilder->build();

        // make assertions
        $this->assertEquals($listMock, $result);
    }

}