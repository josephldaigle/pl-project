<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/18/18
 * Time: 2:09 PM
 */

namespace Test\Unit\Core\Data;


use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordFactory;
use PapaLocal\Core\Data\RecordSet;
use PHPUnit\Framework\TestCase;


/**
 * Class RecordFactoryTest
 *
 * @package Test\Unit\Core\Data
 */
class RecordFactoryTest extends TestCase
{
    public function testCanInstantiate()
    {
        $recordFactory = new RecordFactory();
        $this->assertInstanceOf(RecordFactory::class, $recordFactory);
    }

    public function testCanCreateRecord()
    {
        // set up fixtures
        $properties = array(
            'col1' => 'value1',
            'col2' => 'value2',
            'col3' => 'value3'
        );

        $recordFactory = new RecordFactory();

        // exercise SUT
        $record = $recordFactory->createRecord($properties);

        // make assertions
        $this->assertInstanceOf(Record::class, $record, 'unexpected type');
        $this->assertEquals($properties, $record->properties(), 'unexpected value');
    }

    public function testCanCreateRecordSet()
    {
        $recordMock = $this->createMock(Record::class);

        // set up fixtures
        $records = array($recordMock, $recordMock);

        $recordFactory = new RecordFactory();

        // exercise SUT
        $recordSet = $recordFactory->createRecordSet($records);

        // make assertions
        $this->assertInstanceOf(RecordSet::class, $recordSet, 'unexpected type');
        $this->assertEquals(count($records), $recordSet->count(), 'unexpected set count');
    }


    public function testCanCreateFromQueryResultIsSuccess()
    {
        $recordMock = $this->createMock(Record::class);

        // set up fixtures
        $queryResult = array(
            ['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3'],
            ['col1' => 'value4', 'col2' => 'value5', 'col3' => 'value6'],
        );

        $recordFactory = new RecordFactory();

        // exercise SUT
        $recordSet = $recordFactory->createFromQueryResult($queryResult);

        // make assertions
        $this->assertInstanceOf(RecordSet::class, $recordSet, 'unexpected type');
        $this->assertEquals(count($queryResult), $recordSet->count(), 'unexpected set count');
    }
}