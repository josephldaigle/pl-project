<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 11:19 AM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\Entity;

/**
 * EntityMapper.
 *
 * A default implementation of entity mapper.
 */
class EntityMapper extends DataMapper
{
    /**
     * @inheritdoc
     */
    protected function toEntity(string $className, array $data)
    {
            $class = $this->entityFactory->createFromArray($className, $data);
            return $class;
    }

    /**
     * @inheritdoc
     */
    protected function toTable(Entity $entity)
    {
        return $entity->toArray();
    }
}