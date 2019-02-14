<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/26/17
 * Time: 1:08 PM
 */

namespace PapaLocal\Data\DataMapper;

use PapaLocal\Entity\Entity;
use PapaLocal\Entity\Exception\UnhandledRequestException;

/**
 * Interface DataMapperInterface.
 */
interface DataMapperInterface
{
    /**
     * Maps a table row to an Entity.
     *
     * @param string $className the name of the Entity to create
     * @param array  $data
     * @return mixed|null an Entity loaded with $data, or null
     * @throws UnhandledRequestException if the request should not be handled by this mapper
     */
    public function mapToEntity(string $className, array $data);

    /**
     * Maps an Entity to a table row.
     *
     * @param Entity $entity
     * @return mixed
     * @throws UnhandledRequestException if the request should not be handled by this mapper
     */
    public function mapToTable(Entity $entity);
}