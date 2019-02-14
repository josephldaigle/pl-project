<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 11:03 PM
 */

namespace Test\Unit\Core\Data;


use PapaLocal\Core\Data\Record;
use PHPUnit\Framework\TestCase;


/**
 * Class RecordTest
 *
 * @package Test\Unit\Core\Data
 */
class RecordTest extends TestCase
{
    public function testCanInstantiate()
    {
        $record = new Record(array());
        $this->assertInstanceOf(Record::class, $record);
    }

    /**
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessageRegExp /^(The offset)(.)+(does not exist in)/
     */
    public function testAccessNonExistentIndexProducesError()
    {
        // set up fixtures
        $data = array(
            'one' => 1,
            'two' => 2
        );

        $record = new Record($data);

        // exercise SUT
        $record['badIndex'];
    }

    public function testCanFetchAllProperties()
    {
        // set up fixtures
        $data = array(
            'one' => 1,
            'two' => 2
        );

        $record = new Record($data);

        // exercise SUT
        $result = $record->properties();

        // make assertions
        $this->assertEquals($data, $result);
    }

    public function testCanLoopOverProperties()
    {
        // set up fixtures
        $data = array(
            'one' => 1,
            'two' => 2
        );

        $record = new Record($data);

        // exercise SUT
        foreach($record->properties() as $colName => $value) {
            $this->assertArrayHasKey($colName, $data, 'key not found');
            $this->assertSame($data[$colName], $value, 'values do not match');
        }
    }

    public function isEmptyProvider()
    {
        return [
            'no data' => [[], true],
            'with data' => [['col1' => 'val1'], false]
        ];
    }

    /**
     * @dataProvider isEmptyProvider
     */
    public function testIsEmpty($data, $expectedResult)
    {
        $record = new Record($data);
        $this->assertEquals($expectedResult, $record->isEmpty());
    }

    public function testMergeIsSuccess()
    {
        $data = array(
            'one' => 1,
            'two' => 2
        );

        $data2 = array(
            'three' => 3,
            'four' => 4
        );

        $record = new Record($data);
        $record2 = new Record($data2);

        // exercise SUT
        $result = $record->merge($record2);


        $this->assertContains($data2['three'], $result->properties());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^(Cannot merge two records who share the same key)/
     */
    public function testMergeThrowsExceptionOnDuplicateKeys()
    {
        $data = array(
            'one' => 1,
            'two' => 2
        );

        $data2 = array(
            'two' => 3,
            'four' => 4
        );

        $record = new Record($data);
        $record2 = new Record($data2);

        // exercise SUT
        $record->merge($record2);

    }
}