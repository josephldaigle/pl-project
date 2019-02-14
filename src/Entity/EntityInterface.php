<?php
/**
 * Created by PhpStorm.
 * User: Yacouba
 * Date: 11/15/17
 * Time: 1:28 PM
 */

namespace PapaLocal\Entity;

/**
 * Interface EntityInterface
 *
 * @package PapaLocal\Entity
 */
interface EntityInterface
{
    /**
     * Whether or not implementor is equal to $comparator.
     *
     * @param Entity $comparator
     * @return bool
     */
    public function equals(Entity $comparator): bool;

    /**
     * Whether or not the implementor can be displayed in the feed.
     *
     * @return bool
     */
    public function isFeedItem(): bool;

    /**
     * Converts the object's members to a multi-dimensional associative array.
     *
     * @return array
     */
    public function toArray(): array;
}