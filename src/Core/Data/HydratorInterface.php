<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/17/18
 * Time: 8:22 AM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Entity\Entity;


/**
 * Interface HydratorInterface.
 *
 * Describe a Hydrator
 *
 * @package PapaLocal\Core\Data
 */
interface HydratorInterface
{
    /**
     * Set the entity that will be hydrated.
     *
     * @param  Entity $entity
     * @throws \InvalidArgumentException
     */
    public function setEntity(Entity $entity);

    /**
     * Hydrate the entity.
     *
     * @return Entity
     */
    public function hydrate(): Entity;
}