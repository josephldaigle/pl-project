<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 11:59 AM
 */

namespace PapaLocal\Entity;

/**
 * Interface EntityFactoryInterface.
 */
interface EntityFactoryInterface
{
    /**
     * Creates an empty instance of $class.
     *
     * @param string $class the class name
     * @return mixed
     */
    public function create(string $class);

    /**
     * Retuns an instance of $class with members initialized using $data.
     * @return mixed
     */
    public function createFromArray(string $class, array $data);
}