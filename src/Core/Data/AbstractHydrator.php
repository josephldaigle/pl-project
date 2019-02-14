<?php
/**
 * Created by Ewebify, LLC.
 * Date: 2/17/18
 * Time: 8:04 AM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Entity\Entity;
use PapaLocal\Entity\EntityFactory;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * AbstractHydrator.
 *
 * A Hydrator is a class that loads a complex object from persistence.
 *
 * @package PapaLocal\Core\Data
 */
abstract class AbstractHydrator implements HydratorInterface
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
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var Entity the object to be hydrated
     */
    protected $entity;

	/**
	 * AbstractHydrator constructor.
	 *
	 * @param TableGateway        $tableGateway
	 * @param EntityFactory       $entityFactory
	 * @param SerializerInterface $serializer
	 */
    public function __construct(TableGateway $tableGateway,
							    EntityFactory $entityFactory,
							    SerializerInterface $serializer)
    {
        $this->tableGateway = $tableGateway;
        $this->entityFactory = $entityFactory;
        $this->serializer = $serializer;
    }
}