<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/18/18
 */

namespace Test\Unit\Core\Data;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordSet;
use PHPUnit\Framework\TestCase;


/**
 * Class RecordSetTest.
 *
 * @package Test\Unit\Core\Data
 */
class RecordSetTest extends TestCase
{
    public function testCanInstantiate()
    {
        $recordSet = new RecordSet();

        $this->assertInstanceOf(RecordSet::class, $recordSet);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /^(All elements in argument 1 for)(.)+(construct)(.)+(must implement)/
     */
    public function testConstructorThrowsExceptionWhenInvalidElementsExistInArg()
    {
        // set up fixtures
        $data = array(
            new Record(array('name' => 'testVal', 'description' => 'a test value')),
            new Record(array('name' => 'testVal', 'description' => 'a test value')),
            'An invalid type.',
        );

        // exercise SUT
        $recordSet = new RecordSet($data);
    }

    public function testCanInstantiateWithValidArgs()
    {
        // set up fixtures
        $data = array(
            new Record(array('name' => 'testVal', 'description' => 'a test value')),
            new Record(array('name' => 'testVal', 'description' => 'a test value'))
        );

        // exercise SUT
        $recordSet = new RecordSet($data);

        // make assertions
        $this->assertInstanceOf(RecordSet::class, $recordSet, 'unexpected type');
        $this->assertFalse($recordSet->isEmpty(), 'unexpected isEmpty() result');
        $this->assertEquals(count($data), $recordSet->count(), 'unexpected count');

        foreach($recordSet as $item) {
            $this->assertContains($item, $data, 'record set contains items not found in constructor args', false, true);
        }
    }


    /**
     * @depends testCanInstantiateWithValidArgs
     * if the depended on test changes to not include a for loop, this test should be updated as well
     * this test serves to provide documentation when --testdox are output
     */
    public function testCanIterateElementsUsingFor()
    {
        $this->assertTrue(true);
    }
}