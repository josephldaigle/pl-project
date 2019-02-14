<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 2:29 AM
 */

namespace Test\Unit\Core\Data\Command;


use PapaLocal\Core\Data\Command\StartTransaction;
use PapaLocal\Core\Data\Command\StartTransactionHandler;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class StartTransactionHandlerTest
 *
 * @package Test\Unit\Core\Data\Command
 */
class StartTransactionHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                       ->method('startTransaction');

        $commandMock = $this->createMock(StartTransaction::class);
        $handler = new StartTransactionHandler($tableGatewayMock);

        // exercise SUT
        $handler->__invoke($commandMock);
    }
}