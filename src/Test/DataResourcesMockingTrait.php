<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 7/9/18
 */

namespace PapaLocal\Test;


use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\DataResourcePool;
use PapaLocal\Core\Data\TableGateway;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Trait DataResourcesMockingTrait
 *
 * @package PapaLocal\Test
 *
 * Provide functions commonly used when mocking the database.
 */
trait DataResourcesMockingTrait
{
    /**
     * Fetch a DataResourcePool mock.
     *
     * @param TableGateway        $tableGateway
     * @param SerializerInterface $serializer
     * @param Mapper              $mapper
     * @param CommandFactory      $commandFactory
     *
     * @return mixed
     */
    public function getDataResourcePoolMock(TableGateway $tableGateway, SerializerInterface $serializer, Mapper $mapper, CommandFactory $commandFactory)
    {
        $dataPoolMock = $this->createMock(DataResourcePool::class);
        $dataPoolMock->expects($this->once())
            ->method('getTableGateway')
            ->willReturn($tableGateway);
        $dataPoolMock->expects($this->once())
            ->method('getSerializer')
            ->willReturn($serializer);

        return $dataPoolMock;
    }
}