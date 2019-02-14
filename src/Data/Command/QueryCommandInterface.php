<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/25/17
 * Time: 9:48 PM
 */

namespace PapaLocal\Data\Command;

use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\TableGateway;
use Symfony\Component\Serializer\Serializer;

/**
 * Interface QueryCommandInterface.
 *
 * Describe a QueryCommand.
 */
interface QueryCommandInterface
{
    /**
     * Execute the command.
     *
     * @param TableGateway $tableGateway
     * @param Mapper       $mapper
     * @param Serializer   $serializer
     *
     * @return mixed
     */
    public function execute(TableGateway $tableGateway, Mapper $mapper, Serializer $serializer, CommandFactory $commandFactory);

}