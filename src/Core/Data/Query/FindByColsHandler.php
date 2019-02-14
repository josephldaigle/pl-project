<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/3/18
 * Time: 10:26 PM
 */

namespace PapaLocal\Core\Data\Query;


use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class FindByColsHandler
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByColsHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * FindByColsHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param FindByCols $query
     *
     * @return \PapaLocal\Core\Data\RecordSetInterface
     */
    public function __invoke(FindByCols $query): RecordSetInterface
    {
        $this->tableGateway->setTable($query->getTableName());
        return $this->tableGateway->findByColumns($query->getPredicates());
    }
}