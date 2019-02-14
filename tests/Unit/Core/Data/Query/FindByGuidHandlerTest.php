<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/22/18
 * Time: 4:01 PM
 */

namespace Test\Unit\Core\Data\Query;


use PapaLocal\Core\Data\Query\FindByGuid;
use PapaLocal\Core\Data\Query\FindByGuidHandler;
use PapaLocal\Core\Data\RecordInterface;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class FindByGuidHandlerTest
 *
 * @package Test\Unit\Core\Data\Query
 */
class FindByGuidHandlerTest extends TestCase
{

    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableName = 'TestTableName';
        $guid = '24c2aadf-8dc2-4a08-a020-3e99f979fdaa';

        $queryMock = $this->createMock(FindByGuid::class);
        $queryMock->expects($this->once())
            ->method('getTableName')
            ->willReturn($tableName);
        $queryMock->expects($this->once())
            ->method('getGuid')
            ->willReturn($guid);
        
        $recordMock = $this->createMock(RecordInterface::class);

        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
            ->method('setTable')
            ->with($this->equalTo($tableName));
        $tableGatewayMock->expects($this->once())
            ->method('findByGuid')
            ->with($this->equalTo($guid))
            ->willReturn($recordMock);

        $handler = new FindByGuidHandler($tableGatewayMock);

        // exercise SUT
        $result = $handler->__invoke($queryMock);

        // make assertions
        $this->assertEquals($recordMock, $result, 'unexpected result');
    }
}