<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 3:39 PM
 */


namespace PapaLocal\Data;


use PapaLocal\Core\Data\TableGateway;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Data\Command\QueryCommandInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * DataService.
 *
 * Service class for data persistence.
 *
 * This class provides command dispatching for the persistence layer.
 * @link https://drive.google.com/open?id=0B7JWNEh20SL0ZmYxaS0xb0hwTkU
 *
 * @package PapaLocal\Data
 */
class DataService
{
    /**
     * @var QueryCommand
     */
    protected $command;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * DataService constructor.
     *
     * TODO: Refactor to use DataResourcePool, then refactor into only a director pattern.
     *
     * @param TableGateway  $tableGateway
     * @param Mapper        $mapper
     * @param SerializerInterface $serializer
     * @param CommandFactory $commandFactory
     *
     */
    public function __construct(TableGateway $tableGateway, Mapper $mapper, SerializerInterface $serializer,
                                CommandFactory $commandFactory)
    {
        $this->tableGateway = $tableGateway;
        $this->mapper = $mapper;
        $this->serializer = $serializer;
        $this->commandFactory = $commandFactory;
    }

    /**
     * Set the next command to execute.
     *
     * @param QueryCommandInterface $queryCommand
     */
    public function setCommand(QueryCommandInterface $queryCommand)
    {
        $this->command = $queryCommand;
    }

    /**
     * @param QueryCommand|null $command
     * @return mixed
     */
    public function execute(QueryCommand $command = null)
    {
        if (is_null($command)) {
            if (! $this->command instanceof QueryCommand) {
                // command not supplied
                throw new \BadMethodCallException('Cannot call execute() without first setting a command.');
            }

            //capture current command and reset to null to prevent accidental re-runs
            $command = $this->command;
            $this->command = null;

            //execute the command
            return $command->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);
        }

        //capture current command and reset to null to prevent accidental re-runs
        $this->command = null;

        //execute the command
        return $command->execute($this->tableGateway, $this->mapper, $this->serializer, $this->commandFactory);
    }
}