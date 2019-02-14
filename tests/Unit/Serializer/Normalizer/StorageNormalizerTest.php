<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/22/18
 * Time: 8:10 PM
 */

namespace Test\Unit\Serializer\Normalizer;

use PapaLocal\Serializer\Normalizer\StorageNormalizer;
use PapaLocal\Test\TestDummyTwo;
use PHPUnit\Framework\TestCase;

/**
 * StorageNormalizerTest.
 *
 * @package Test\Unit\Serializer\Normalizer
 */
class StorageNormalizerTest extends TestCase
{
    /**
     * @dataProvider supportsNormalizationProvider
     */
    public function testSupportsNormalization($format, $expected)
    {
        // set up fixtures
        $normalizerMock = $this->getMockBuilder(StorageNormalizer::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['supportsNormalization'])
            ->getMock();

        // exercise SUT
        $actual = $normalizerMock->supportsNormalization(array(), $format);
        
        // make assertions
        $this->assertSame($expected, $actual, 'unexpected result for '. $format);
    }

    /**
     * @return array
     */
    public function supportsNormalizationProvider()
    {
        return [
            ['array', true],
            ['object', false]
        ];
    }

    public function testNormalizerOnlyIncludesSpecifiedFieldsOnSuccess()
    {
        // set up fixtures
        $normalizerMock = $this->getMockBuilder(StorageNormalizer::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept(['normalize'])
            ->getMock();

        $testDummy = (new TestDummyTwo())
            ->setMember('valueOne')
            ->setMemberTwo('valueTwo');

        // exercise SUT
        // should only normalize the 'member' property
        $normalized = $normalizerMock->normalize($testDummy, 'array', array('attributes' => array('member')));

        // make assertions
        $this->assertArrayHasKey('member', $normalized);
        $this->assertArrayNotHasKey('memberTwo', $normalized);
    }
}