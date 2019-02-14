<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/7/18
 * Time: 12:09 PM
 */

namespace Test\Unit\Data\Command;

use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Data\DataMapper\Mapper;
use PapaLocal\Core\Data\TableGateway;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

/**
 * QueryCommandTest.
 */
class QueryCommandTest extends TestCase
{
    public function testQueryCommandCallsRunQueryMethod()
    {
        // set up fixtures
        $control = 'This is a control variable.';

        // mock a TableGateway
        $tblGateMock = $this->getMockBuilder(TableGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        // mock a Mapper
        $mapperMock = $this->getMockBuilder(Mapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        // mock a serializer
        $serializerMock = $this->getMockBuilder(Serializer::class)
            ->getMock();

        // mock command factory
        $commandFacMock = $this->createMock(CommandFactory::class);

        // mock SUT
        $cmdMock = $this->getMockBuilder(QueryCommand::class)
            ->setMethodsExcept(['execute'])
            ->getMockForAbstractClass();
        $cmdMock->expects($this->once())
            ->method('runQuery')
            ->willReturn($control);

        // exercise SUT
        $result = $cmdMock->execute($tblGateMock, $mapperMock, $serializerMock, $commandFacMock);

        // make assertions
        $this->assertSame($control, $result, 'unexpected return value');
    }
}