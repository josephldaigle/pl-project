<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 1:22 AM
 */

namespace PapaLocal\Core\Data\Command;


use PapaLocal\Core\Data\TableGatewayInterface;


/**
 * Class RollbackTransactionHandler
 *
 * @package PapaLocal\Core\Data\Command
 */
class RollbackTransactionHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * RollbackTransactionHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Undoes any un-commited changes performed since last commit.
     *
     * @param RollbackTransaction $command
     */
    function __invoke(RollbackTransaction $command)
    {
        $this->tableGateway->rollbackTransaction();
        return;
    }

}