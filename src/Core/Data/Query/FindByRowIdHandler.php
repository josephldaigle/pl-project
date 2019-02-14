<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 4:09 PM
 */

namespace PapaLocal\Core\Data\Query;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class FindByRowIdHandler
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByRowIdHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * FindByIdHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param FindByRowId $query
     *
     * @return \PapaLocal\Core\Data\RecordInterface
     */
    function __invoke(FindByRowId $query)
    {
        $this->tableGateway->setTable($query->getTableName());
        $record = $this->tableGateway->findById($query->getRowId());

        return $record;
    }
}