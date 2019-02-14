<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/17/18
 * Time: 11:10 PM
 */

namespace Test\Unit\Core\Data;


use PapaLocal\Core\Data\MySqlConnection;
use Doctrine\DBAL\Driver\Statement;
use PapaLocal\Core\Data\AdaptedTableGateway;
use PapaLocal\Core\Data\Record;
use PapaLocal\Core\Data\RecordFactory;
use PapaLocal\Core\Data\RecordSet;
use PapaLocal\Core\Data\WrappedQueryBuilder;
use PHPUnit\Framework\TestCase;


/**
 * Class AdaptedTableGatewayTest
 *
 * @package Test\Unit\Core\Data
 */
class AdaptedTableGatewayTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testCreateIsSuccess()
    {
        // set up fixtures
        $data = array(
            'one' => 1,
            'two' => 2,
        );

        $tableName = 'SomeTable';

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('insert')
                         ->with($tableName);
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('setValue')
                         ->withConsecutive(
                             [$this->equalTo('one'), $this->equalTo('?')],
                             [$this->equalTo('two'), $this->equalTo('?')]
                         );
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('setParameter')
                         ->withConsecutive(
                             [0, 1, $this->isNull()],
                             [1, 2, $this->isNull()]
                         );
        $queryBuilderMock->expects($this->once())
                         ->method('execute');

        $connectionMock    = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $tableGateway->create($data);

    }

    public function testUpdateIsSuccess()
    {
        // set up fixtures
        $data = array(
            'one' => 1,
            'two' => 2,
        );

        $tableName = 'SomeTable';

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('update')
                         ->with($this->equalTo($tableName), $this->isNull());
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('set');
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('setParameter');
        $queryBuilderMock->expects($this->once())
                         ->method('execute');

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $tableGateway->update($data);
    }

    public function testDeleteIsSuccess()
    {
        // set up fixtures
        $rowId     = 1;
        $tableName = 'SomeTable';

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('delete')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('where')
                         ->with($this->equalTo('id = ?'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('setParameter')
                         ->with($this->equalTo(0), $this->equalTo($rowId))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute');

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $tableGateway->delete($rowId);
    }

    public function testFindByIsSuccess()
    {
        // set up fixtures
        $recordSetMock = $this->createMock(RecordSet::class);

        $col   = 'ColName';
        $value = 'Some value to search for';

        $tableName     = 'SomeTable';
        $qryResultData = array(
            ['col1' => 'row1col1', 'col2' => 'row1col2'],
            ['col1' => 'row2col1', 'col2' => 'row2col2'],
        );

        $statementMock = $this->createMock(Statement::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('select')
                         ->with($this->equalTo('*'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('from')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('where')
                         ->with($this->equalTo($col.' = ?'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('setParameter')
                         ->with($this->equalTo(0), $this->equalTo($value))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute')
                         ->willReturn($statementMock);

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $statementMock->expects($this->once())
                      ->method('fetchAll')
                      ->willReturn($qryResultData);

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $recordFactoryMock->expects($this->once())
                          ->method('createFromQueryResult')
                          ->with($this->equalTo($qryResultData))
                          ->willReturn($recordSetMock);


        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $result = $tableGateway->findBy($col, $value);

        $this->assertEquals($recordSetMock, $result);
    }

    public function testFindByIdIsSuccess()
    {
        // set up fixtures
        $id            = 1;
        $tableName     = 'SomeTable';
        $qryResultData = array(
            ['col1' => 'row1col1', 'col2' => 'row1col2'],
        );

        $recordMock = $this->createMock(Record::class);

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())
                      ->method('fetchAll')
                      ->willReturn($qryResultData);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('select')
                         ->with($this->equalTo('*'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('from')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('where')
                         ->with($this->equalTo('id = ?'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('setParameter')
                         ->with($this->equalTo(0), $this->equalTo($id))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute')
                         ->willReturn($statementMock);

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);
        $recordFactoryMock->expects($this->once())
                          ->method('createRecord')
                          ->with($this->equalTo($qryResultData[0]))
                          ->willReturn($recordMock);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $result = $tableGateway->findById($id);

        // make assertions
        $this->assertEquals($recordMock, $result);
    }

    public function testFindByGuidIsSuccess()
    {
        // set up fixtures
        $guid          = 'c10c0c21-c414-4631-9d67-b1994f5299a5';
        $tableName     = 'SomeTable';
        $qryResultData = array(
            ['col1' => 'row1col1', 'col2' => 'row1col2'],
        );

        $recordMock = $this->createMock(Record::class);

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())
                      ->method('fetchAll')
                      ->willReturn($qryResultData);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('select')
                         ->with($this->equalTo('*'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('from')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('where')
                         ->with($this->equalTo('guid = ?'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('setParameter')
                         ->with($this->equalTo(0), $this->equalTo($guid))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute')
                         ->willReturn($statementMock);

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);
        $recordFactoryMock->expects($this->once())
                          ->method('createRecord')
                          ->with($this->equalTo($qryResultData[0]))
                          ->willReturn($recordMock);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $result = $tableGateway->findByGuid($guid);

        $this->assertEquals($recordMock, $result);
    }

    public function testFindAllOrderedByIdIsSuccess()
    {
        // set up fixtures
        $tableName     = 'SomeTable';
        $qryResultData = array(
            ['col1' => 'row1col1', 'col2' => 'row1col2'],
            ['col1' => 'row2col1', 'col2' => 'row2col2'],
        );

        $recordSetMock = $this->createMock(RecordSet::class);

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())
                      ->method('fetchAll')
                      ->willReturn($qryResultData);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('select')
                         ->with($this->equalTo('*'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('from')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('orderBy')
                         ->with($this->equalTo('id'), $this->equalTo('ASC'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute')
                         ->willReturn($statementMock);

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);
        $recordFactoryMock->expects($this->once())
                          ->method('createFromQueryResult')
                          ->with($this->equalTo($qryResultData))
                          ->willReturn($recordSetMock);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $result = $tableGateway->findAllOrderedById();

        $this->assertEquals($recordSetMock, $result);
    }

    public function testFindByColumnsIsSuccess()
    {
        // set up fixtures
        $filter    = array(
            'col1' => 'row1Col1',
            'col2' => 'row1Col2',
        );
        $tableName = 'SomeTable';

        $qryResultData = array(
            ['col1' => 'row1col1', 'col2' => 'row1col2'],
            ['col1' => 'row2col1', 'col2' => 'row2col2'],
        );

        $recordSetMock = $this->createMock(RecordSet::class);

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())
                      ->method('fetchAll')
                      ->willReturn($qryResultData);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);
        $queryBuilderMock->expects($this->once())
                         ->method('select')
                         ->with($this->equalTo('*'))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('from')
                         ->with($this->equalTo($tableName))
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('andWhere')
                         ->withConsecutive([$this->equalTo('col1 = :col1')], [$this->equalTo('col2 = :col2')])
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))
                         ->method('setParameter')
                         ->withConsecutive(
                             [$this->equalTo('col1'), $this->equalTo('row1Col1')],
                             [$this->equalTo('col2'), $this->equalTo('row1Col2')])
                         ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
                         ->method('execute')
                         ->willReturn($statementMock);

        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($queryBuilderMock);

        $recordFactoryMock = $this->createMock(RecordFactory::class);
        $recordFactoryMock->expects($this->once())
                          ->method('createFromQueryResult')
                          ->with($this->equalTo($qryResultData))
                          ->willReturn($recordSetMock);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->setTable($tableName);
        $result = $tableGateway->findByColumns($filter);

        $this->assertEquals($recordSetMock, $result);
    }

    public function testStartTransactionIsSuccess()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(false);
        $connectionMock->expects($this->once())
                       ->method('startTransaction');

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->startTransaction();
    }

    public function testStartTransactionDoesNothingWhenTransactionAlreadyStarted()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(true);
        $connectionMock->expects($this->never())
                       ->method('startTransaction');

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->startTransaction();
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot call ROLLBACK when a transaction has not been started
     */
    public function testRollBackTransactionThrowsExceptionWhenTransactionNotStarted()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(false);
        $connectionMock->expects($this->never())
                       ->method('rollbackTransaction');

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->rollbackTransaction();
    }

    public function testRollbackTransactionIsSuccess()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(true);
        $connectionMock->expects($this->once())
                       ->method('rollbackTransaction');

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->rollbackTransaction();
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot call COMMIT when a transaction has not been started
     */
    public function testCommitTransactionThrowsExceptionWhenTransactionNotStarted()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(false);
        $connectionMock->expects($this->never())
                       ->method('commitTransaction');

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->commitTransaction();
    }

    public function testCommitTransactionIsSuccess()
    {
        // set up fixtures
        $connectionMock = $this->createMock(MySqlConnection::class);
        $connectionMock->expects($this->once())
                       ->method('isTransactionActive')
                       ->willReturn(true);
        $connectionMock->expects($this->once())
                       ->method('commitTransaction');

        $recordFactoryMock = $this->createMock(RecordFactory::class);

        $queryBuilderMock = $this->createMock(WrappedQueryBuilder::class);

        $tableGateway = new AdaptedTableGateway($connectionMock, $recordFactoryMock);

        // exercise SUT
        $tableGateway->commitTransaction();
    }
}