<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 2:35 AM
 */

namespace Test\Unit\Core\Data\Command;


use PapaLocal\Core\Data\Command\RollbackTransaction;
use PapaLocal\Core\Data\Command\RollbackTransactionHandler;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class RollbackTransactionHandlerTest
 *
 * @package Test\Unit\Core\Data\Command
 */
class RollbackTransactionHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                       ->method('rollbackTransaction');

        $commandMock = $this->createMock(RollbackTransaction::class);

        $handler = new RollbackTransactionHandler($tableGatewayMock);

        // exercise SUT
        $handler->__invoke($commandMock);
    }
}