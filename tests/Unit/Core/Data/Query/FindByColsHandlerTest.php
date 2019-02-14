<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/3/18
 * Time: 10:28 PM
 */

namespace Test\Unit\Core\Data\Query;


use PapaLocal\Core\Data\Query\FindByCols;
use PapaLocal\Core\Data\Query\FindByColsHandler;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\RecordSetInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class FindByColsHandlerTest
 *
 * @package Test\Unit\Core\Data\Query
 */
class FindByColsHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableName = 'TestTableName';
        $predicates = array(
            'col1' => 'val1',
            'col2' => 'val2'
        );

        $queryMock = $this->createMock(FindByCols::class);
        $queryMock->expects($this->once())
                  ->method('getTableName')
                  ->willReturn($tableName);
        $queryMock->expects($this->once())
                  ->method('getPredicates')
                  ->willReturn($predicates);

        $recordSetMock = $this->createMock(RecordSetInterface::class);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                         ->method('setTable')
                         ->with($this->equalTo($tableName));
        $tableGatewayMock->expects($this->once())
                         ->method('findByColumns')
                         ->with($this->equalTo($predicates))
                         ->willReturn($recordSetMock);

        $handler = new FindByColsHandler($tableGatewayMock);

        // exercise SUT
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($recordSetMock, $result, 'unexpected result');
    }
}