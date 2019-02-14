<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/6/18
 * Time: 9:09 PM
 */

namespace PapaLocal\Core\ValueObject\Collection;


use PapaLocal\Entity\Collection\Collection;


/**
 * Interface ListBuilderInterface
 *
 * @package PapaLocal\Core\ValueObject\Collection
 */
interface ListBuilderInterface
{
    /**
     * @param        $object
     * @param string $key
     *
     * @return ListBuilderInterface
     */
    public function add($object, string $key = ''): ListBuilderInterface;

    /**
     * Builds the list.
     *
     * @return mixed
     */
    public function build(): Collection;
}