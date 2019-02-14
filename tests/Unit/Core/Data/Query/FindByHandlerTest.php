<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/25/18
 * Time: 1:04 PM
 */

namespace Test\Unit\Core\Data\Query;


use PapaLocal\Core\Data\Query\FindBy;
use PapaLocal\Core\Data\Query\FindByHandler;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByHandlerTest
 *
 * @package Test\Unit\Core\Data\Query
 */
class FindByHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableName = 'TestTableName';
        $column = 'colName';
        $value = 'someValue';

        $queryMock = $this->createMock(FindBy::class);
        $queryMock->expects($this->once())
                  ->method('getTableName')
                  ->willReturn($tableName);
        $queryMock->expects($this->once())
                  ->method('getColumnName')
                  ->willReturn($column);
        $queryMock->expects($this->once())
                  ->method('getValue')
                  ->willReturn($value);

        $recordSetMock = $this->createMock(RecordSetInterface::class);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo($tableName));
        $tableGatewayMock->expects($this->once())
                         ->method('findBy')
                         ->with($this->equalTo($column), $this->equalTo($value))
                         ->willReturn($recordSetMock);

        $handler = new FindByHandler($tableGatewayMock);

        // exercise SUT
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($recordSetMock, $result, 'unexpected result');
    }
}