<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 11/1/18
 * Time: 4:36 PM
 */

namespace Test\Unit\Core\Data\Exception;

use PapaLocal\Core\Data\Exception\CommandException;
use PapaLocal\Core\Data\Exception\CommandExceptionCode;
use PHPUnit\Framework\TestCase;


/**
 * Class CommandExceptionTest
 *
 * @package Test\Unit\Core\Data\Exception
 */
class CommandExceptionTest extends TestCase
{
    public function testCanInstantiate()
    {
        $exception = new CommandException('Test exception message.', CommandExceptionCode::NOT_FOUND());

        // make assertions
        $this->assertInstanceOf(CommandException::class, $exception, 'unexpected type');
        $this->assertSame(CommandExceptionCode::NOT_FOUND()->getValue(), $exception->getCode());
    }
}