<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/10/18
 * Time: 11:10 AM
 */

namespace PapaLocal\Data\Command\Admin;


use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Entity\Exception\QueryCommandFailedException;

/**
 * ExportTablesAsCsv.
 *
 * Exports all mysql tables to csv files and stores them in the local dev directory.
 *
 * @package PapaLocal\Data\Command\Admin
 */
class FetchTableNames extends QueryCommand
{
    /**
     * @inheritDoc
     */
    protected function runQuery()
    {
        try {
            $qb = $this->tableGateway->connection->createQueryBuilder();

            $qb->select('table_name')
                ->from('information_schema.tables')
                ->where('table_schema = ?')
                ->setParameter(0, $this->tableGateway->connection->getDatabase());


            $result = $qb->execute()->fetchAll();

            if (! count($result) > 1) {
                throw new QueryCommandFailedException(sprintf('Unexpected row count returned by %s: %s in %s',
                    __CLASS__, count($result), $this->tableGateway->connection->getDatabase()));
            }

            // filter out views and return result
            return array_filter($result, function($row){
                return (strpos($row['table_name'], 'v_') !== 0);
            });

        } catch (\Exception $exception) {
            throw $this->filterException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    protected function filterException(\Exception $exception): \Exception
    {
        return $exception;
    }

}