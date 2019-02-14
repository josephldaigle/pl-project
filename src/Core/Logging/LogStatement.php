<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/14/18
 * Time: 11:27 AM
 */

namespace PapaLocal\Core\Logging;


/**
 * Class LogStatement
 *
 * Model a log statement that is structured to be saved in the database.
 *
 * @package PapaLocal\Core\Logging
 */
class LogStatement
{
	private $message;
	private $userId;

	/**
	 * LogStatement constructor.
	 *
	 * @param string $message
	 * @param int    $userId
	 */
	public function __construct(string $message, int $userId = null)
	{
		$this->message = $message;
		$this->userId = $userId;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->userId;
	}
}