<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/26/18
 */

namespace PapaLocal\Entity\Collection;


/**
 * Interface CollectionInterface.
 *
 * Describe a Collection.
 *
 * @package PapaLocal\Entity\Collection
 */
interface CollectionInterface extends \IteratorAggregate, \Countable
{

    /**
     * Add an element to the collection.
     *
     * @param      $object
     * @param null $key if null, the underlying list will be numerically indexed.
     * @return mixed
     * @throws \InvalidArgumentException    if $object is null or empty, or if the $key is already assigned.
     */
    public function add($object, $key = null);

    /**
     * Whether or not an item exists at index $key.
     *
     * @param $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * Fetch an item from the collection.
     *
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($key);

    /**
     * Fetch the first item in the collection.
     *
     * @return mixed|null
     */
    public function first();

    /**
     * Fetch the last item in the collection.
     *
     * @return mixed|null
     */
    public function last();

    /**
     * Find an item in the collection by it's $property containing $value.
     *
     * @param $property
     * @param $value
     * @return mixed returns null if the collection is empty or the item is not found
     * @throws \BadMethodCallException if the collection contains primitives
     */
    public function findBy($property, $value);

    /**
     * Fetch all items in the collection as an array.
     *
     * @return array
     */
    public function all();

    /**
     * Remove an item from the collection.
     *
     * @param $key
     * @throws \InvalidArgumentException
     */
    public function remove($key);

    /**
     * Replace an element in the collection.
     *
     * @param $object
     * @param $key
     */
    public function replace($object, $key);

    /**
     * @return int number of elements in the collection
     */
    public function count(): int;

    /**
     * @return \Generator|\Traversable
     */
    public function getIterator();
}