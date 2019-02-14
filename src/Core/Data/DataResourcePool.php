<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/27/18
 * Time: 10:18 PM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class DataResourcePool
 *
 * Provides a way to pass around resources commonly used by the data layer.
 *
 * @package PapaLocal\Core\Data
 */
class DataResourcePool
{
	/**
	 * @var TableGateway
	 */
	private $tableGateway;

	/**
	 * @var SerializerInterface
	 */
	private $serializer;

	/**
	 * @var Mapper
	 */
	public $mapper;

	/**
	 * @var CommandFactory
	 */
	public $commandFactory;

	/**
	 * DataResourcePool constructor.
	 *
	 * @param TableGateway        $tableGateway
	 * @param SerializerInterface $serializer
	 * @param Mapper              $mapper
	 * @param CommandFactory      $commandFactory
	 */
	public function __construct(TableGateway $tableGateway,
	                            SerializerInterface $serializer,
	                            Mapper $mapper,
	                            CommandFactory $commandFactory)
	{
		$this->tableGateway = $tableGateway;
		$this->serializer = $serializer;
		$this->mapper = $mapper;
		$this->commandFactory = $commandFactory;
	}

	/**
	 * @return TableGateway
	 */
	public function getTableGateway(): TableGateway
	{
		return $this->tableGateway;
	}

	/**
	 * @return SerializerInterface
	 */
	public function getSerializer(): SerializerInterface
	{
		return $this->serializer;
	}

    /**
     * @return Mapper
     */
    public function getMapper(): Mapper
    {
        return $this->mapper;
    }

    /**
     * @return CommandFactory
     */
    public function getCommandFactory(): CommandFactory
    {
        return $this->commandFactory;
    }

}