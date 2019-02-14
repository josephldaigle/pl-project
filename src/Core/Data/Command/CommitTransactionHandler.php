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
 * Class CommitTransactionHandler
 *
 * @package PapaLocal\Core\Data\Command
 */
class CommitTransactionHandler
{
    /**
     * @var TableGatewayInterface
     */
    private $tableGateway;

    /**
     * CommitTransactionHandler constructor.
     *
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Permanently save any changes since the last commit.
     *
     * @param CommitTransaction $command
     */
    function __invoke(CommitTransaction $command)
    {
        $this->tableGateway->commitTransaction();
        return;
    }

}