<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 1:48 AM
 */

namespace PapaLocal\Core\Data;


use Doctrine\DBAL\Connection;


/**
 * Class MySqlConnection
 *
 * @package PapaLocal\Core\Data
 */
class MySqlConnection implements DbConnectionInterface
{
    /**
     * @var Connection the connection object used to connect to MySql
     */
    private $connection;

    /**
     * MySqlConnection constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return bool
     */
    public function isTransactionActive(): bool
    {
        return $this->connection->isTransactionActive();
    }

    /**
     * Start a transaction.
     * @return void
     */
    public function startTransaction()
    {
        $this->connection->beginTransaction();
        return;
    }

    /**
     * @return mixed|void
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollbackTransaction()
    {
        $this->connection->rollBack();
        return;
    }

    /**
     * @return mixed|void
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commitTransaction()
    {
        $this->connection->commit();
    }

    /**
     * @return WrappedQueryBuilder
     */
    public function getQueryBuilder(): WrappedQueryBuilder
    {
        return new WrappedQueryBuilder($this->connection->createQueryBuilder());
    }

}