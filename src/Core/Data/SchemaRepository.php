<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 6/17/18
 * Time: 8:19 PM
 */


namespace PapaLocal\Core\Data;


use PapaLocal\Entity\Exception\ServiceOperationFailedException;


/**
 * Class SchemaRepository
 *
 * @package PapaLocal\Core\Data
 */
class SchemaRepository extends AbstractRepository
{
	/**
	 * Fetch the column names for a table.
	 *
	 * @param string $table
	 *
	 * @return array
	 * @throws ServiceOperationFailedException
	 */
	public function fetchColumnNames(string $table)
	{
		$qb = $this->tableGateway->connection->createQueryBuilder();

		$qb->select('column_name')
		   ->from('information_schema.columns')
		   ->where('table_schema = ?')
			->andWhere('table_name = ?')
			->setParameter(0, $this->tableGateway->connection->getDatabase())
			->setParameter(1, $table);

		$rows = $qb->execute()->fetchAll();

		if (! count($rows) > 1) {
			throw new ServiceOperationFailedException(sprintf('Unexpected row count returned by %s: %s in %s',
				__CLASS__, count($rows), $this->tableGateway->connection->getDatabase()));
		}

		$cols = array();
		foreach ($rows as $row) {
			$cols[] = $row['column_name'];
		}

		// filter out views and return result
		return $cols;
	}
}