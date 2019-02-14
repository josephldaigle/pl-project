<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 1:50 AM
 */

namespace PapaLocal\Core\Data;


/**
 * Interface DbConnectionInterface
 *
 * Describe a Database connection.
 *
 * @package PapaLocal\Core\Data
 */
interface DbConnectionInterface extends TransactionalInterface
{
    /**
     * @return bool whether or not a transaction has begun
     */
    public function isTransactionActive(): bool;
}