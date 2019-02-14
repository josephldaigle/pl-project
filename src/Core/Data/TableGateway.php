<?php
/**
 * Created by Ewebify, LLC.
 * Date: 11/24/17
 * Time: 6:54 PM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Core\Exception\InvalidStateException;
use Doctrine\DBAL\Connection;


/**
 * Class TableGateway.
 *
 * Table-level CRUD operations.
 *
 * @package PapaLocal\Core\Data
 */
class TableGateway implements TransactionalInterface
{
    /**
     * @var Connection connection object to use for querying the database.
     */
    public $connection;

    /**
     * @var string the table to execute queries against.
     */
    private $table;

    /**
     * TableGateway constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set the table to use for the next query.
     *
     * @param string $tableName
     */
    public function setTable(string $tableName)
    {
        $this->table = $tableName;
    }

    /**
     * Create a new record in the table.
     *
     * @param array $row the data to insert.
     * @return int the id of the inserted record
     */
    public function create(array $row)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //set the table
        $qb->insert($this->table);

        //set values
        $suppliedCols = array_keys($row);
        for ($i = 0; $i < count($suppliedCols); $i++) {
            $colName = $suppliedCols[$i];
            $bindVal = $row[$colName];

            $qb->setValue($colName, '?');
            $qb->setParameter($i, $bindVal);
        }

        //execute the query
        $qb->execute();

        //get insert id
        $id = intval($this->connection->lastInsertId());

        //return results
        return $id;
    }

    /**
     * Update a record in the table.
     *
     * @param array $row an associative array with indexed by column name.
     * @return int the number of rows affected
     */
    public function update(array $row)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //set the table
        $qb->update($this->table);

        //set query params
        foreach ($row as $colName => $value) {
            if ($colName === 'id') {
                $qb->where('id = :id');
                $qb->setParameter('id', $value);
            } else {
                $qb->set($colName, ':' . $colName);
                $qb->setParameter($colName, $value);
            }
        }

        //return query results
        return $qb->execute();
    }

    /**
     * Delete a record by id.
     *
     * @param int $id
     * @return int the number of rows deleted
     */
    public function delete(int $id)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //execute query
        $qb->delete($this->table)
            ->where('id = ?')
            ->setParameter(0, $id);

        //return query result
        return $qb->execute();
    }

    /**
     * Find a record with $val in $col.
     *
     * @param string    $column
     * @param mixed     $value
     * @return array empty when no records found
     */
    public function findBy(string $column, $value)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //execute query
        $qb->select('*')
            ->from($this->table)
            ->where($column . ' = ?')
            ->setParameter(0, $value);

        //return query result
        return $qb->execute()->fetchAll();
    }

    /**
     * Find a record by it's id.
     *
     * @param int $id
     * @return array empty when no records found
     */
    public function findById(int $id)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //execute query
        $qb->select('*')
            ->from($this->table)
            ->where('id = ?')
            ->setParameter(0, $id);


        //return query result
        return $qb->execute()->fetchAll();
    }

    public function findByGuid(string $guid)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //execute query
        $qb->select('*')
            ->from($this->table)
            ->where('guid = ?')
            ->setParameter(0, $guid);


        //return query result
        return $qb->execute()->fetchAll();
    }

    /**
     * Fetch all rows from the table, sorted by id.
     * @return array empty when no records found
     */
    public function findAllOrderedById()
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        //define query
        $qb->select('*')
            ->from($this->table)
            ->orderBy('id', 'ASC');

        //execute and return result
        return $qb->execute()->fetchAll();
    }

    public function findByColumns(array $cols)
    {
        //get query builder
        $qb = $this->connection->createQueryBuilder();

        $qb->select('*')
            ->from($this->table);

        foreach($cols as $colName => $value) {
            $qb->andWhere($colName . ' = :' . $colName)
                ->setParameter($colName, $value);
        }

        return $qb->execute()->fetchAll();
    }

	/**
	 * Starts the database transaction.
     *
     * @deprecated
	 */
    public function beginTransaction()
    {
    	if ($this->connection->isTransactionActive()) {
		    return;
	    }

	    $this->connection->beginTransaction();
    }

    /**
     * Commit the transaction.
     *
     * @deprecated
     *
     * @throws InvalidStateException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commit()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new InvalidStateException("Cannot call COMMIT when a transaction has not been started.");
        }

        $this->connection->commit();
    }

    /**
     * Rollback changes.
     *
     * @deprecated
     *
     * @throws InvalidStateException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollback()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new InvalidStateException("Cannot call ROLLBACK when a transaction has not been started.");
        }

        $this->connection->rollBack();
    }

    /**
     * @return mixed|void
     */
    public function startTransaction()
    {
        if ($this->connection->isTransactionActive()) {
            return;
        }

        $this->connection->beginTransaction();
    }

    /**
     * @return mixed|void
     * @throws InvalidStateException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollbackTransaction()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new InvalidStateException("Cannot call ROLLBACK when a transaction has not been started.");
        }

        $this->connection->rollBack();
    }

    /**
     * @return mixed|void
     * @throws InvalidStateException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commitTransaction()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new InvalidStateException("Cannot call COMMIT when a transaction has not been started.");
        }

        $this->connection->commit();
    }


}