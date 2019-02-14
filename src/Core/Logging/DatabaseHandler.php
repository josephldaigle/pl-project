<?php
/**
 * Created by eWebify, LLC.
 * Author: Joe Daigle
 * Date: 8/13/18
 * Time: 8:14 PM
 */


namespace PapaLocal\Core\Logging;


use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Bridge\Monolog\Logger;


/**
 * Class DatabaseHandler
 *
 * @package PapaLocal\Core\Logging
 */
class DatabaseHandler extends AbstractProcessingHandler
{
	/**
	 * @var LogRepository
	 */
	private $logRepository;

    /**
     * @var GuidGeneratorInterface
     */
	private $guidGenerator;

	/**
	 * DatabaseHandler constructor.
	 *
	 * @param bool|int      $level
	 * @param bool          $bubble
	 */
	public function __construct(
        $level = Logger::DEBUG, $bubble = TRUE
    )
    {
		parent::__construct($level, $bubble);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record)
	{
		// do nothing for each write record
		// this class collects all logs for the request in the handleBatch() function
		return;
	}

	public function handleBatch(array $records)
	{

		$serialized = '';
		foreach($records as $record) {

			$logStmt = array(
				'channel' => $record['channel'],
				'level' => $record['level'],
				'message' => $record['message'],
				'time' => $record['datetime']->format('U'),
			);

			$serialized .= serialize($logStmt);
		}

		$logStmt = $this->createLogStatement($serialized);

		$this->logRepository->save($this->guidGenerator->generate()->value(), $logStmt);
	}


	/**
	 * If the repository is injected using __construct, Symfony will throw a
	 * doctrine cache error, due to circular reference to the connection object.
	 *
	 * It is believed this is caused (not verified) by the order in which configuration
	 * files are parsed (config_dev.yml before services.yml).
	 *
	 * This function allows injecting repository without the error.
	 *
	 *
	 * @param LogRepository $logRepository
	 */
	public function setRepository(LogRepository $logRepository)
	{
		$this->logRepository = $logRepository;
	}

    /**
     * @param GuidGeneratorInterface $guidGenerator
     */
	public function setGuidGenerator(GuidGeneratorInterface $guidGenerator)
	{
		$this->guidGenerator = $guidGenerator;
	}

	/**
	 * Create a LogStatement value object.
	 *
	 * @param string $serializeMessage
	 * @param int    $userId
	 *
	 * @return LogStatement
	 */
	private function createLogStatement(string $serializeMessage, int $userId = null)
	{
		return new LogStatement($serializeMessage, $userId);
	}
}