<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/13/18
 * Time: 8:43 PM
 */


namespace PapaLocal\Core\Logging;


use PapaLocal\Core\Data\AbstractRepository;


/**
 * Class LogRepository
 *
 * @package PapaLocal\Core\Logging
 */
class LogRepository extends AbstractRepository
{
	/**
	 * Save a log entry.
	 *
	 * @param string          $id
	 * @param LogStatement $logStatement
	 */
	public function save(string $id, LogStatement $logStatement)
	{
		$this->tableGateway->setTable('Log');
		$this->tableGateway->create(array(
			'id' => $id,
			'messages' => $logStatement->getMessage(),
			'userId' => $logStatement->getUserId()
		));

		return;
	}

	/**
	 * Load all logs.
	 *
	 * @return array
	 */
	public function load()
	{
		$this->tableGateway->setTable('Log');
		$rows = $this->tableGateway->findAllOrderedById();

		return $rows;
	}
}