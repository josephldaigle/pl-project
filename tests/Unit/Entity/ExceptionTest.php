<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/4/17
 */

namespace Test\Unit\Entity;

use PapaLocal\Entity\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ExceptionTest.
 *
 */
class ExceptionTest extends TestCase
{

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testCanInstantiateExceptions($exceptionName)
    {
        $sut = new $exceptionName();

        $this->assertInstanceOf($exceptionName, $sut);
    }

    public function exceptionDataProvider()
    {
        return [
            [ Exception\QueryCommandFailedException::class ],
            [ Exception\QueryException::class ],
            [ Exception\SetterNotFoundException::class ],
            [ Exception\UnhandledRequestException::class ],
            [ Exception\UsernameExistsException::class ],
            [ Exception\UserNotFoundException::class ],
        ];
    }
}