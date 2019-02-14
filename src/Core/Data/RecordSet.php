<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 9/14/18
 */


namespace PapaLocal\Core\Data;


/**
 * Class RecordSet.
 *
 * Model the result of a sql query.
 *
 * @package PapaLocal\Core\Data
 */
class RecordSet implements RecordSetInterface
{
    /**
     * @var array 
     */
    private $records = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * RecordSet constructor.
     *
     * @param array $records
     */
    public function __construct(array $records = [])
    {
        $this->setRecords($records);
        $this->position = 0;
    }

    /**
     * @param array $records
     * @throws \InvalidArgumentException if any of the elements in $records is not an instance of RecordInterface
     */
    protected function setRecords(array $records) {
        foreach($records as $key => $val) {
            if (! $val instanceof RecordInterface) {
                throw new \InvalidArgumentException(sprintf('All elements in argument 1 for %s::__construct() must implement %s',
                    __CLASS__, RecordInterface::class));
            }

            $this->records[] = $val;
        }
    }

    /**
     * Check if the record set contains data.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return ($this->count() === 0);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->records[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->records[$this->position]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }
}