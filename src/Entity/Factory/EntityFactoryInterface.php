<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/5/17
 * Time: 11:59 AM
 */

namespace PapaLocal\Entity\Factory;

/**
 * Interface EntityFactoryInterface.
 */
interface EntityFactoryInterface
{
    /**
     * @return mixed an empty instance of object.
     */
    public function create(string $class);

    /**
     * @return mixed an instance of object.
     */
    public function createFromArray(string $class, array $data);
}