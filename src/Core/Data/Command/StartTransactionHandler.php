<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 1:19 AM
 */

namespace PapaLocal\Core\Data\Command;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class StartTransactionHandler
 *
 * @package PapaLocal\Core\Data\Command
 */
class StartTransactionHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * StartTransactionHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param StartTransaction $command
     */
    function __invoke(StartTransaction $command)
    {
        $this->tableGateway->startTransaction();
        return;
    }

}