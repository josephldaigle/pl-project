<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/18/18
 */

namespace PapaLocal\Core\Data;

/**
 * Class RecordFactory.
 *
 * @package PapaLocal\Core\Data
 */
class RecordFactory
{
    /**
     * @param array $properties
     *
     * @return Record
     */
    public function createRecord(array $properties): Record
    {
        return new Record($properties);
    }

    /**
     * Requires $records arg to contain only instances of Record.
     *
     * @param array $records
     *
     * @return RecordSet
     */
    public function createRecordSet(array $records = []): RecordSet
    {
        return new RecordSet($records);
    }

    /**
     * @param array $rows an array of arrays, where each sub-array
     * contains a $key => $val mapping of one row of data.
     *
     * $rows = array(
     *      array('col1' => 'value1', 'col2' => 'value2'),
     *      array('col1' => 'value3', 'col2' => 'value4'),
     * )
     *
     * @return RecordSet
     */
    public function createFromQueryResult(array $rows): RecordSet
    {
        array_walk($rows, function(&$item, $key, RecordFactory $recordFactory) { $item = $recordFactory->createRecord($item); }, $this);
        return $this->createRecordSet($rows);
    }
}