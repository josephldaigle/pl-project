<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/25/17
 * Time: 10:02 PM
 */

namespace PapaLocal\Data\Command;

use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\TableGateway;
use Symfony\Component\Serializer\Serializer;

/**
 * QueryCommand.
 *
 */
abstract class QueryCommand implements QueryCommandInterface
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * @inheritdoc
     */
    final public function execute(TableGateway $tableGateway, Mapper $mapper, Serializer $serializer,
                                  CommandFactory $commandFactory)
    {
        //set gateway and mapper
        $this->tableGateway = $tableGateway;
        $this->mapper = $mapper;
        $this->serializer = $serializer;
        $this->commandFactory = $commandFactory;

        //execute the command
        $commandResult = $this->runQuery();
        return $commandResult;
    }

    /**
     * Executes the code to run a query against the provided connection.
     *
     * @return mixed
     * @throws \Exception exception thrown is varies by implementor
     */
    protected abstract function runQuery();

    /**
     * Filters exceptions received while performing database
     * operations.
     *
     * Implement inside sub-classes to control error-reporting
     * in a way that is useful to clients.
     *
     * @param \Exception $exception
     * @return \Exception
     */
    protected abstract function filterException(\Exception $exception): \Exception;
}