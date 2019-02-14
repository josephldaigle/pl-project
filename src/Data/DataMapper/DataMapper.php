<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/22/17
 * Time: 10:08 PM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use PapaLocal\Entity\Exception\UnhandledRequestException;

abstract class DataMapper implements DataMapperInterface
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var DataMapper
     */
    protected $successor;

    /**
     * DataMapperFactory constructor.
     *
     * @param EntityFactory $entityFactory
     * @param DataMapper $successor
     */
    public function __construct(EntityFactory $entityFactory = null, DataMapper $successor = null)
    {
        if (! is_null($entityFactory)) {
            $this->entityFactory = $entityFactory;
        }

        $this->successor = $successor;
    }

    /**
     * @inheritdoc
     */
    final public function mapToEntity(string $className, array $data)
    {
        try {

            //try to handle this request
            $entity = $this->toEntity($className, $data);

            //request handled, return result
            return $entity;

        } catch (UnhandledRequestException $ure) {

            //not handled, try to pass off to successor
            if ($this->successor !== null) {

                return $this->successor->mapToEntity($className, $data);

            } else {

                //successor does not exist
                throw new UnhandledRequestException('End of mapper chain reached.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    final public function mapToTable(Entity $entity)
    {
        try {

            //try to handle this request
            $row = $this->toTable($entity);

            //request handled, return result
            return $row;

        } catch (UnhandledRequestException $ure) {

            //not handled, pass off to successor
            if ($this->successor !== null) {
                return $this->successor->mapToTable($entity);
            } else {
                //request not handled, and no successor
                throw new UnhandledRequestException('End of mapper chain reached.');
            }
        }
    }

    /**
     * Request Handlers.
     *
     * Implement these functions in sub-classes to provide case-specific handling.
     *
     */

    /**
     * Map a table row to an entity.
     *
     * @param string $className
     * @param array  $data an array containing $key => $value pairs that
     *      correspond to the members in $className
     *
     * @return Entity
     * @throws UnhandledRequestException
     */
    abstract protected function toEntity(string $className, array $data);

    /**
     * Map an Entity to a table row.
     *
     * @param Entity $entity
     * @return mixed
     * @throws UnhandledRequestException
     */
    abstract protected function toTable(Entity $entity);
}