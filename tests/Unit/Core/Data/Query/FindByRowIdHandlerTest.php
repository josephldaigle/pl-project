<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 4:13 PM
 */

namespace Test\Unit\Core\Data\Query;


use PapaLocal\Core\Data\Query\FindByRowId;
use PapaLocal\Core\Data\Query\FindByRowIdHandler;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class FindByRowIdHandlerTest
 *
 * @package Test\Unit\Core\Data\Query
 */
class FindByRowIdHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableName = 'TestTableName';
        $rowId = 1;

        $queryMock = $this->createMock(FindByRowId::class);
        $queryMock->expects($this->once())
                  ->method('getTableName')
                  ->willReturn($tableName);
        $queryMock->expects($this->once())
                  ->method('getRowId')
                  ->willReturn($rowId);

        $recordMock = $this->createMock(RecordInterface::class);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo($tableName));
        $tableGatewayMock->expects($this->once())
                         ->method('findById')
                         ->with($this->equalTo($rowId))
                         ->willReturn($recordMock);

        $handler = new FindByRowIdHandler($tableGatewayMock);

        // exercise SUT
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($recordMock, $result, 'unexpected result');
    }
}