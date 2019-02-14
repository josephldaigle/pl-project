<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/25/17
 * Time: 11:07 PM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;

/**
 * Mapper.
 *
 * This is the Service class in the application.
 * @see app/config/services.yml
 */
class Mapper implements DataMapperInterface
{
    /**
     * @var MapperFactory
     */
    private $mapperFactory;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * Mapper constructor.
     *
     * @param MapperFactory $mapperFactory
     * @param EntityFactory $entityFactory
     */
    public function __construct(MapperFactory $mapperFactory, EntityFactory $entityFactory)
    {
        $this->mapperFactory = $mapperFactory;
        $this->entityFactory = $entityFactory;
    }

    /**
     * Maps a table row to an Entity.
     *
     * @param string $className the name of the Entity to create
     * @param array  $data
     * @return mixed|null an Entity loaded with $data, or null
     */
    final public function mapToEntity(string $className, array $data)
    {
        $chain = $this->getChain();
        return $chain->mapToEntity($className, $data);
    }

    /**
     * Maps an Entity to a table row.
     *
     * @param Entity $entity
     * @return mixed
     */
    final public function mapToTable(Entity $entity)
    {
        $chain = $this->getChain();
        return $chain->mapToTable($entity);
    }

    /**
     * Produces a chain of data mappers to handle the given request.
     *
     * @return DataMapperInterface
     */
    protected function getChain()
    {
        return $this->mapperFactory->create($this->entityFactory, CreditCardMapper::class,
                $this->mapperFactory->create($this->entityFactory, UserMapper::class,
                $this->mapperFactory->create($this->entityFactory, EntityMapper::class)));
    }
}