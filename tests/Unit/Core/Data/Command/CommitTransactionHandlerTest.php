<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/21/18
 * Time: 2:43 AM
 */

namespace Test\Unit\Core\Data\Command;


use PapaLocal\Core\Data\Command\CommitTransaction;
use PapaLocal\Core\Data\Command\CommitTransactionHandler;
use PapaLocal\Core\Data\TableGatewayInterface;
use PHPUnit\Framework\TestCase;


/**
 * Class CommitTransactionHandlerTest
 *
 * @package Test\Unit\Core\Data\Command
 */
class CommitTransactionHandlerTest extends TestCase
{
    public function testHandlerIsSuccess()
    {
        // set up fixtures
        $tableGatewayMock = $this->createMock(TableGatewayInterface::class);
        $tableGatewayMock->expects($this->once())
                       ->method('commitTransaction');

        $commandMock = $this->createMock(CommitTransaction::class);

        $handler = new CommitTransactionHandler($tableGatewayMock);

        // exercise SUT
        $handler->__invoke($commandMock);
    }
}