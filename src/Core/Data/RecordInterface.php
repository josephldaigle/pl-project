<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/18/18
 */


namespace PapaLocal\Core\Data;


/**
 * Interface RecordInterface.
 *
 * Describe a row of data as an array.
 *
 * @package PapaLocal\Core\Data
 */
interface RecordInterface extends \ArrayAccess
{
    /**
     * Fetch an array containing col => val mapping for the record.
     *
     * @return array
     */
    public function properties(): array;

    /**
     * Whether or not the record contains data.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Merges two records.
     *
     * @param RecordInterface $record
     *
     * @return RecordInterface
     * @throws \LogicException if a key exists in both Records (duplicate)
     */
    public function merge(RecordInterface $record): RecordInterface;
}