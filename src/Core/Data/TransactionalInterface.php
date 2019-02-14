<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 9/26/18
 * Time: 8:33 PM
 */


namespace PapaLocal\Core\Data;


/**
 * Interface DatabaseTransactionInterface
 *
 * Describe a transactional interface for database connections.
 *
 * @package PapaLocal\Core\Data
 */
interface TransactionalInterface
{
    /**
     * Start a database transaction.
     *
     * @return mixed
     * @throws \BadMethodCallException if startTransaction() is not called before invoking this method.
     */
    public function startTransaction();

    /**
     * Rollback all database operations since startTransaction() was last called.
     *
     * @return mixed
     * @throws \BadMethodCallException if startTransaction() is not called before invoking this method.
     */
    public function rollbackTransaction();

    /**
     * Commit all database operations since startTransaction() was last called.
     *
     * @return mixed
     * @throws \BadMethodCallException if startTransaction() is not called before invoking this method.
     */
    public function commitTransaction();
}