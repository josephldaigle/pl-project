<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 10/27/17
 */

namespace PapaLocal\Test;

use Doctrine\DBAL\DriverManager;
use PHPUnit\DbUnit\Operation\Factory;
use \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use \PHPUnit\DbUnit\TestCaseTrait;

/**
 * Class AbstractDatabaseTestCase.
 *
 * Provides database abstraction for unit testing with Symfony.
 *
 * WARNING: If setUp() is overridden in this class, or by classes
 *   that extend this one, it MUST include a call to
 *   parent::setUp(). If call to parent is omitted,test cases
 *   may exhibit unexpected behavior.
 */
abstract class AbstractDatabaseTestCase extends KernelTestCase
{
    use TestCaseTrait;

    /**
     * @var PDO connection.
     */
    static private $pdo = null;

    /**
     * @var DBAL connection.
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

            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }

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
     * @return \Doctrine\DBAL\Connection|null
     */
    protected function getDbal()
    {
        if (self::$dbal === null) {
            self::$dbal = DriverManager::getConnection(array('pdo' => $this->getPdo()));
        }

        return self::$dbal;
    }

    /**
     * Truncate tables after each test case.
     *
     * @return \PHPUnit\DbUnit\Operation\Operation
     */
    protected function getTearDownOperation()
    {
        return Factory::TRUNCATE();
    }
}