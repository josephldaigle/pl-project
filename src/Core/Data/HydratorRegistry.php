<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 8/19/18
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Entity\Collection\Collection;


/**
 * Class HydratorRegistry.
 *
 * A registry of object hydrators.
 *
 * @package PapaLocal\Core\Data
 */
class HydratorRegistry extends Collection
{
    /**
     * @inheritdoc
     */
    public function add($repository, $key = null)
    {
        if (! $repository instanceof AbstractHydrator){
            throw new \InvalidArgumentException(sprintf('%s expects param 1 to be an instance of %s.', __METHOD__, AbstractHydrator::class));
        }

        parent::add($repository, $key);
    }
}