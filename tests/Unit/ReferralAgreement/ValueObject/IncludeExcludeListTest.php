<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/27/18
 * Time: 10:43 AM
 */

namespace Test\Unit\ReferralAgreement\ValueObject;


use PapaLocal\ReferralAgreement\ValueObject\IncludeExcludeList;
use PapaLocal\ReferralAgreement\ValueObject\Location;
use PapaLocal\ReferralAgreement\ValueObject\LocationType;
use PHPUnit\Framework\TestCase;


/**
 * Class IncludeExcludeListTest
 *
 * @package Test\Unit\ReferralAgreement\ValueObject
 */
class IncludeExcludeListTest extends TestCase
{
    public function testCanInstantiate()
    {
        $list = new IncludeExcludeList();

        $this->assertInstanceOf(IncludeExcludeList::class, $list);
    }

    public function testFilterReturnsCorrectResult()
    {
        // set up fixtures
        $includedTypeMock = $this->createMock(LocationType::class);
        $includedTypeMock->expects($this->any())
            ->method('getValue')
            ->willReturn('include');

        $includeMock = $this->createMock(Location::class);

        $includeMock->expects($this->any())
            ->method('getLocation')
            ->willReturn('Include Location');
        $includeMock->expects($this->any())
            ->method('getType')
            ->willReturn($includedTypeMock);

        $excludeMock = $this->createMock(Location::class);
        $excludedTypeMock = $this->createMock(LocationType::class);
        $excludedTypeMock->expects($this->any())
                         ->method('getValue')
                         ->willReturn('exclude');

        $excludeMock->expects($this->any())
                    ->method('getLocation')
                    ->willReturn('Exclude Location');
        $excludeMock->expects($this->any())
                    ->method('getType')
                    ->willReturn($excludedTypeMock);

        $data = array($includeMock, $includeMock, $excludeMock);

        $list = new IncludeExcludeList($data);

        // exercise SUT
        $includesResult = $list->getIncludes();

        // make assertions
        $this->assertEquals(2, $includesResult->count(), 'unexpected includes count');
        $this->assertSame('include', $includesResult->first()->getType()->getValue(), 'unexpected includes type');

        // exercise SUT
        $excludesResult = $list->getExcludes();

        // make assertions
        $this->assertEquals(1, $excludesResult->count(), 'unexpected excludes count');
        $this->assertSame('exclude', $excludesResult->first()->getType()->getValue(), 'unexpected excludes type');


    }
}