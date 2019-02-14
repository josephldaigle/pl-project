<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 11/28/17
 */

namespace PapaLocal\Test;

use PapaLocal\Data\DataMapper\DataMapperInterface;
use PapaLocal\Entity\Entity;

/**
 * Class DataMapperDummy.
 *
 * @package PapaLocal\Test
 */
class DataMapperDummy implements DataMapperInterface
{
    public function mapToEntity(string $className, array $data)
    {
        return;
    }

    public function mapToTable(Entity $entity)
    {
        return;
    }

}