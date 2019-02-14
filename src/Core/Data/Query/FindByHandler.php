<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/23/18
 */

namespace PapaLocal\Core\Data\Query;


use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class FindByHandler.
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * FindByHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param FindBy $query
     *
     * @return RecordSetInterface
     */
    public function __invoke(FindBy $query): RecordSetInterface
    {
        $this->tableGateway->setTable($query->getTableName());
        $records = $this->tableGateway->findBy($query->getColumnName(), $query->getValue());

        return $records;
    }


}