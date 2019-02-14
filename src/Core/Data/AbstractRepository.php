<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/28/18
 * Time: 7:16 PM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * Class AbstractRepository
 *
 * @package PapaLocal\Core\Data
 */
abstract class AbstractRepository
{
	/**
	 * @var TableGateway
	 */
	protected $tableGateway;

	/**
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * @var Mapper
	 */
	protected $mapper;

	/**
	 * @var CommandFactory
	 */
	protected $commandFactory;

	/**
	 * AbstractRepository constructor.
	 *
	 * @param DataResourcePool $dataResourcePool
	 */
	public function __construct(DataResourcePool $dataResourcePool)
	{
		$this->tableGateway = $dataResourcePool->getTableGateway();
		$this->serializer = $dataResourcePool->getSerializer();
		$this->mapper = $dataResourcePool->mapper;
		$this->commandFactory = $dataResourcePool->commandFactory;
	}
}