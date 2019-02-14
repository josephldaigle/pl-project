<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 10:53 PM
 */


namespace PapaLocal\Core\Data;


/**
 * Class AdaptedTableGateway
 *
 * Uses the Record and RecordSet classes, and replaces TableGateway.
 *
 * @package PapaLocal\Core\Data
 */
class AdaptedTableGateway implements TableGatewayInterface
{
    /**
     * @var MySqlConnection
     */
    private $connection;

    /**
     * @var RecordFactory
     */
    private $recordFactory;

    /**
     * @var string the table to execute queries against.
     */
    private $table;

    /**
     * AdaptedTableGateway constructor.
     *
     * @param MySqlConnection     $connection
     * @param RecordFactory       $recordFactory
     */
    public function __construct(
        MySqlConnection $connection,
        RecordFactory $recordFactory
    )
    {
        $this->connection    = $connection;
        $this->recordFactory = $recordFactory;
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
     * @param array $record
     */
    public function create(array $record)
    {
        $qb = $this->getQueryBuilder();

        //set the table
        $qb->insert($this->table);

        //set values
        $suppliedCols = array_keys($record);
        for ($i = 0; $i < count($suppliedCols); $i++) {
            $colName = $suppliedCols[$i];
            $bindVal = $record[$colName];

            $qb->setValue($colName, '?');
            $qb->setParameter($i, $bindVal);
        }

        //execute the query
        $qb->execute();

        //return results
        return;
    }

    /**
     * @param array $record
     */
    public function update(array $record)
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //set the table
        $qb->update($this->table);

        //set query params
        foreach ($record as $colName => $value) {
            if ($colName === 'id') {
                // set where clause
                $qb->where('id = :id');
                $qb->setParameter('id', $value);
            } else {
                // set column params
                $qb->set($colName, ':' . $colName);
                $qb->setParameter($colName, $value);
            }
        }

        // execute query
        $qb->execute();

        return;
    }

    /**
     * @param int $id
     */
    public function delete(int $id)
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //execute query
        $qb->delete($this->table)
           ->where('id = ?')
           ->setParameter(0, $id);

        $qb->execute();

        //return query result
        return;
    }

    /**
     * @param string $column
     * @param        $value
     *
     * @return RecordSetInterface
     */
    public function findBy(string $column, $value): RecordSetInterface
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //execute query
        $qb->select('*')
           ->from($this->table)
           ->where($column . ' = ?')
           ->setParameter(0, $value);

        $rows = $qb->execute()->fetchAll();

        $recordSet = $this->recordFactory->createFromQueryResult($rows);

        //return query result
        return $recordSet;
    }

    /**
     * @param int $id
     *
     * @return RecordInterface
     */
    public function findById(int $id): RecordInterface
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //execute query
        $qb->select('*')
           ->from($this->table)
           ->where('id = ?')
           ->setParameter(0, $id);

        $rows = $qb->execute()->fetchAll();

        $record = $this->recordFactory->createRecord($rows[0]);

        //return query result
        return $record;
    }

    /**
     * @param string $guid
     *
     * @return RecordInterface
     */
    public function findByGuid(string $guid): RecordInterface
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //execute query
        $qb->select('*')
           ->from($this->table)
           ->where('guid = ?')
           ->setParameter(0, $guid);

        $rows = $qb->execute()->fetchAll();

        //return query result
        if (count($rows) > 0) {

            return $this->recordFactory->createRecord($rows[0]);
        } else {
            return $this->recordFactory->createRecord([]);
        }
    }

    /**
     * @return RecordSetInterface
     */
    public function findAllOrderedById(): RecordSetInterface
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        //define query
        $qb->select('*')
           ->from($this->table)
           ->orderBy('id', 'ASC');

        //execute and return result
        $rows = $qb->execute()->fetchAll();

        $recordSet = $this->recordFactory->createFromQueryResult($rows);

        return $recordSet;
    }

    /**
     * @param array $filter
     *
     * @return RecordSetInterface
     */
    public function findByColumns(array $filter): RecordSetInterface
    {
        //get query builder
        $qb = $this->getQueryBuilder();

        $qb->select('*')
           ->from($this->table);

        foreach($filter as $colName => $value) {
            $qb->andWhere($colName . ' = :' . $colName)
               ->setParameter($colName, $value);
        }

        $rows = $qb->execute()->fetchAll();
        $recordSet = $this->recordFactory->createFromQueryResult($rows);
        return $recordSet;
    }

    /**
     * @return void
     */
    public function startTransaction()
    {
        if ($this->connection->isTransactionActive()) {
            return;
        }

        $this->connection->startTransaction();
        return;
    }

    /**
     * @return void
     * @throws \BadMethodCallException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollbackTransaction()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new \BadMethodCallException("Cannot call ROLLBACK when a transaction has not been started.");
        }

        $this->connection->rollbackTransaction();
        return;
    }

    /**
     * @return void
     * @throws \BadMethodCallException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commitTransaction()
    {
        if (! $this->connection->isTransactionActive())
        {
            throw new \BadMethodCallException("Cannot call COMMIT when a transaction has not been started.");
        }

        $this->connection->commitTransaction();
        return;
    }

    /**
     * {@inheritdoc}
     * 
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder(): QueryBuilderInterface
    {
        return $this->connection->getQueryBuilder();
    }
}