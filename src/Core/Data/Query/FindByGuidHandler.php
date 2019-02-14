<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 3:57 PM
 */

namespace PapaLocal\Core\Data\Query;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class FindByGuidHandler
 *
 * @package PapaLocal\Core\Data\Query
 */
class FindByGuidHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * FindByGuidHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param FindByGuid $query
     *
     * @return \PapaLocal\Core\Data\RecordInterface
     */
    function __invoke(FindByGuid $query)
    {
        $this->tableGateway->setTable($query->getTableName());
        $row = $this->tableGateway->findByGuid($query->getGuid());
        return $row;
    }
}