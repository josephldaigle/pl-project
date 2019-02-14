<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/18/18
 * Time: 2:08 PM
 */

namespace PapaLocal\Test;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\TestCaseTrait;

/**
 * WebDatabaseTestCase.
 *
 * NOTE: If you extend this class, you MUST call setTables($tableNames) from the implementing
 * class's setUp function.
 *
 * The connection object in this class is used to interact with the database
 * outside of the Symfony ecosystem, when running tests. For example, you might:
 *  1) use this connection to query the number of rows in a table
 *  2) add a row to the table to test a feature of the data layer (using the app's data layer)
 *  3) query again for the number of rows, and compare that the table row count is incremented
 *
 * This allows testing the components of the data layer are not affected by test-related querying.
 *
 * @package PapaLocal\Test
 */
abstract class WebDatabaseTestCase extends WebTestCase
{
    use TestCaseTrait;

	/**
	 * @var CsvDataSet
	 */
	private $dataSet;

    /**
     * @var \PDO connection pdo resource creating using environment settings
     */
    static private $pdo = null;

    /**
     * @var Connection
     */
    static private $dbal = null;

    /**
     * @var Connection resource used by DBUnit.
     */
    private $conn = null;

    /**
     * @inheritdoc
     * @return null|\PHPUnit\DbUnit\Database\DefaultConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {

            // create a PDO Conn obj using environment settings
            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }

            // wrap PDO conn in DBUnit\DefaultConnection
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    /**
     * Fetch PDO connection object.
     * @return \PDO
     */
    protected function getPdo()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * Fetch DBAL connection (wrapper) from DriverManager, using the pdo connection.
     *
     * @return Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDbal()
    {
        if (self::$dbal === null) {
            self::$dbal = DriverManager::getConnection(array('pdo' => $this->getPdo()));
        }

        return self::$dbal;
    }

    /**
     * Truncate tables after each test case. Without this code,
     * data would remain in the test db tables indefinitely, having
     * potentially adverse effects on subsequent tests.
     *
     * @return \PHPUnit\DbUnit\Operation\Operation
     */
    protected function getTearDownOperation()
    {
        return Factory::TRUNCATE();
    }


	/**
	 * Used to load csv data sets into the database tables needed for each test.
	 *
	 * @return CsvDataSet
	 */
	protected function getDataSet()
	{
		return $this->dataSet;
	}

	/**
	 * Allow subclasses to control which tables are loaded per each test.
	 *
	 * @internal this function's behavior is affected by the environments configuration settings.
	 *
	 * @param array $tableNames     leave empty to load all available tables (DB_CSV_DIR in phpunit.xml)
	 *
	 * @throws \InvalidArgumentException if the name provided cannot be mapped to a data file.
	 */
	public function configureDataSet(array $tableNames = [])
	{
        // create a data set obj
        $dataSet = $this->createDataSet();
        $testDataDir = $GLOBALS['DB_CSV_DIR'];

	    // load all tables if arg is empty
	    if (count($tableNames) < 1) {
            foreach (preg_grep('/(.)+(.csv)$/', scandir($testDataDir)) as $filename) {
                $tableNames[] = pathinfo($filename, PATHINFO_FILENAME);
            }
        }

        // at this point, no matter what, tableNames contains the intenxed tables to load, now order and load them
        // order the tables according to spec
        $configOrder = $this->loadTableOrderConfig();

	    $orderedTableNames = array_intersect($configOrder, $tableNames);

	    if (count($orderedTableNames) < count($tableNames)) {
	        throw new \InvalidArgumentException(sprintf('Attempted to load the following tables into the dataset, but no ordering information was supplied in the the table_order.php file: %s.', implode(',', array_diff($tableNames, $orderedTableNames))));
        }

		// check if a file exists for each table, and if it's readable, then add it to the index
		foreach ($orderedTableNames as $tableName) {
			$filename = $testDataDir . $tableName . '.csv';

			if (! is_readable($filename)) {
				throw new \InvalidArgumentException(sprintf('Unable to read the file %s when loading test data. Did you forget to create the file (see database:export-tables terminal command)?', $filename));
			}

			$dataSet->addTable($tableName, $filename);
		}

		$this->dataSet = $dataSet;
    }

	/**
	 * Factory function for the CsvDataSet.
	 *
	 * @return CsvDataSet
	 */
	private function createDataSet()
	{
		return new CsvDataSet();
	}

    /**
     * @return array
     * @throws \RuntimeException
     */
	private function loadTableOrderConfig(): array
    {
        if (! isset($GLOBALS['TEST_ROOT_DIR']) || ! isset($GLOBALS['TABLE_ORDER_CONFIG_FILE'])) {
            throw new \RuntimeException('The properties TEST_ROOT_DIR and TABLE_ORDER_CONFIG_FILE must both be set in the test environment to auto-configure database test data. If not, then table names must be provided to configureDataSet() when called.');
        }

        $filename = $GLOBALS['TEST_ROOT_DIR'] . $GLOBALS['TABLE_ORDER_CONFIG_FILE'];
        if (!is_readable($filename)) {
            throw new \RuntimeException(sprintf('The file %s must be present in order to auto configure all tables.', $filename));
        }

        $tableOrder = include $filename;

        return $tableOrder;
    }
}