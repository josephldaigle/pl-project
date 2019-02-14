<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/16/18
 * Time: 1:25 PM
 */

namespace PapaLocal\Data\Command\Factory;

use PapaLocal\Data\Command\QueryCommand;

/**
 * CommandFactory.
 *
 * Factory for creating QueryCommand instances.
 */
class CommandFactory
{
    /**
     * Creates a new QueryCommand object.
     *
     * @param string $class             the query command to create
     * @param array  $args              Arguments should be passed in the order defined by the QueryCommands __constructor.
     * @return QueryCommand
     * @throws \ReflectionException
     */
    public function createCommand(string $class, array $args = array()): QueryCommand
    {
        $objectReflection = new \ReflectionClass($class);
        if (count($args) < 1) {
            return $objectReflection->newInstance();
        }

        $object = $objectReflection->newInstanceArgs($args);
        return $object;
    }

}