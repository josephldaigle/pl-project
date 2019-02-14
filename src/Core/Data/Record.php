<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 10:41 PM
 */

namespace PapaLocal\Core\Data;


/**
 * Class Record
 *
 * Model a single row from a database table or view.
 *
 * @package PapaLocal\Core\Data
 */
class Record implements RecordInterface
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * Record constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->properties[] = $value;
        } else {
            $this->properties[$offset] = $value;
        }
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->properties[$offset]);
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->properties[$offset]);
    }

    /**
     * {@inheritdoc}
     * @param mixed $offset
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            return $this->properties[$offset];
        }

        throw new \OutOfBoundsException(sprintf('The offset %s does not exist in %s.', $offset, __CLASS__));
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function properties(): array
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (count($this->properties()) > 0) ? false: true;
    }

    /**
     * @param RecordInterface $record
     *
     * @return RecordInterface
     * @throws \LogicException
     */
    public function merge(RecordInterface $record): RecordInterface
    {
        foreach($record->properties() as $key => $value) {
            if (array_key_exists($key, $this->properties)) {
                throw new \LogicException(sprintf('Cannot merge two records who share the same key(s): %s.', $key));
            }

            $this->properties[$key] = $value;
        }

        return $this;
    }

}