<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/23/17
 * Time: 11:56 PM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\EntityFactory;

/**
 * MapperFactory.
 */
class MapperFactory
{
    /**
     * Creates DataMapperFactory instances.
     *
     * @param EntityFactory   $entityFactory
     * @param string          $mapperName
     * @param DataMapper|null $successor
     *
     * @return DataMapperInterface
     * @throws \InvalidArgumentException
     */
    public function create(EntityFactory $entityFactory, string $mapperName = '', DataMapper $successor = null): DataMapperInterface
    {
        //return the requested mapper
        if (! class_exists($mapperName)) {
            throw new \InvalidArgumentException(sprintf('Unable to load class: %s.', $mapperName));
        }

        $instance = new $mapperName($entityFactory, $successor);

        return $instance;
    }

    /**
     * Create a FormMapper instance.
     *
     * @param EntityFactory $entityFactory
     * @param string        $mapperName
     */
    public function createFormMapper(EntityFactory $entityFactory, string $mapperName): FormMapperInterface
    {
        if (! class_exists($mapperName)) {
            throw new \InvalidArgumentException(sprintf('Unable to load class: %s.', $mapperName));
        }

        $instance = new $mapperName($entityFactory);

        return $instance;
    }
}