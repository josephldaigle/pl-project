<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 9:14 PM
 */

namespace Test\Unit\Core\Data;


use Doctrine\DBAL\Connection;
use PapaLocal\Core\Data\MySqlConnection;
use PHPUnit\Framework\TestCase;


/**
 * Class MySqlConnectionTest
 *
 * @package Test\Unit\Core\Data
 */
class MySqlConnectionTest extends TestCase
{
    public function delegateProvider()
    {
        return [
            'testIsTransactionActive' => ['isTransactionActive', 'isTransactionActive', false],
            'testStartTransaction' => ['startTransaction', 'beginTransaction'],
            'testRollbackTransaction' => ['rollbackTransaction', 'rollback'],
            'testCommitTransaction' => ['commitTransaction', 'commit'],
        ];
    }

    /**
     * @dataProvider delegateProvider
     * @param $sutMethod
     * @param $delegate
     */
    public function testDelegatesCalls($sutMethod, $delegate, $delegateReturn = null)
    {
        $connectionMock = $this->createMock(Connection::class);

        if (isset($delegateReturn))
        {
            $connectionMock->expects($this->once())
                           ->method($delegate)
                           ->willReturn($delegateReturn);
        } else {
            $connectionMock->expects($this->once())
                           ->method($delegate);
        }

        $mysqlConn = new MySqlConnection($connectionMock);

        $mysqlConn->$sutMethod();
    }
}