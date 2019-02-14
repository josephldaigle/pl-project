<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 1:21 AM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Core\Data\Command\CommitTransaction;
use PapaLocal\Core\Data\Command\RollbackTransaction;
use PapaLocal\Core\Data\Command\StartTransaction;
use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\Query\FindByCols;
use PapaLocal\Core\Data\Query\FindByGuid;
use PapaLocal\Core\Data\Query\FindByRowId;
use PapaLocal\Core\ValueObject\GuidInterface;


/**
 * Class AbstractMessageFactory
 *
 * @package PapaLocal\Core\Data
 */
abstract class AbstractMessageFactory
{
    /*********************
     * Commands
     ********************/

    /**
     * @return StartTransaction
     */
    final public function newStartTransaction(): StartTransaction
    {
        return new StartTransaction();
    }

    /**
     * @return RollbackTransaction
     */
    final public function newRollbackTransaction(): RollbackTransaction
    {
        return new RollbackTransaction();
    }

    /**
     * @return CommitTransaction
     */
    final public function newCommitTransaction(): CommitTransaction
    {
        return new CommitTransaction();
    }

    /*********************
     * Queries
     ********************/

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $value
     *
     * @return FindBy
     */
    public function newFindBy(string $tableName, string $columnName, string $value): FindBy
    {
        return new FindBy($tableName, $columnName, $value);
    }

    /**
     * @param string        $tableName
     * @param GuidInterface $guid
     *
     * @return FindByGuid
     */
    public function newFindByGuid(string $tableName, GuidInterface $guid): FindByGuid
    {
        return new FindByGuid($tableName, $guid);
    }

    /**
     * @param string $tableName
     * @param array  $filter
     *
     * @return FindByCols
     */
    public function newFindByCols(string $tableName, array $filter): FindByCols
    {
        return new FindByCols($tableName, $filter);
    }

    /**
     * @param string $tableName
     * @param int    $rowId
     *
     * @return FindByRowId
     */
    public function newFindByRowId(string $tableName, int $rowId): FindByRowId
    {
        return new FindByRowId($tableName, $rowId);
    }
}