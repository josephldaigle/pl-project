<?php

/**
 * Created by Ewebify, LLC.
 * Date: 1/16/18
 * Time: 3:25 PM
 */

namespace Test\Unit\Data\Command\Factory;

use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\Command\QueryCommand;
use PapaLocal\Data\Command\User\HasAccount;
use PapaLocal\Entity\User;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

/**
 * CommandFactoryTest.
 */
class CommandFactoryTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->commandFactory = new CommandFactory();
    }

    public function testCanInstantiateCommand()
    {
        // set up fixtures
        $user = $this->createMock(User::class);

        // exercise SUT
        $command = $this->commandFactory->createCommand(HasAccount::class, array($user));

        // make assertions
        $this->assertInstanceOf(QueryCommand::class, $command);
    }

    /**
     * @expectedException Error
     * @expectedExceptionMessageRegExp /(Too few arguments to function)(.)+(__construct)/
     */
    public function testCreateCommandThrowsErrorWhenMissingCommandConstructorArg()
    {
        // exercise SUT
        $this->commandFactory->createCommand(HasAccount::class, array());
    }
}