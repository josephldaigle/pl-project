<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 2:31 PM
 */


namespace Test\Unit\Core\Factory;


use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\Core\ValueObject\Guid;
use PHPUnit\Framework\TestCase;


/**
 * Class GuidFactoryTest
 *
 * @package Test\Unit\Core\Factory
 */
class GuidFactoryTest extends TestCase
{
    public function testCanInstantiateFactory()
    {
        $factory = new GuidFactory();

        $this->assertInstanceOf(GuidFactory::class, $factory);
    }

    public function testCanCreateGuidObject()
    {
        $factory = new GuidFactory();
        $guid = $factory->generate();

        $this->assertInstanceOf(Guid::class, $guid, 'unexpected type');
        $this->assertNotEmpty($guid->value(), 'value() should not be empty');
        $this->assertEquals(36, strlen($guid->value()), 'value() should be exactly 36 chars long');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(Param 1 supplied)(.)+(must be a 36 character long string)/
     */
    public function testCreateFromStringThrowsExceptionWhenInvalid()
    {
        // set up fixtures
        $value = '044f256b';

        // exercise SUT
        $guidFactory = new GuidFactory();
        $guidFactory->createFromString($value);

    }

    public function testCanCreateGuidFromString()
    {
        // set up fixtures
        $value = '044f256b-b61b-4541-8a15-0eb8569a4453';

        // exercise SUT
        $guidFactory = new GuidFactory();
        $result = $guidFactory->createFromString($value);

        // make assertions
        $this->assertInstanceOf(Guid::class, $result, 'unexpected type');
        $this->assertSame($value, $result->value(), 'unexpected value');
    }
}