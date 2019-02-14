<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/6/18
 * Time: 3:06 PM
 */


namespace Test\Unit\Core\Security;


use PapaLocal\Core\Security\Cryptographer;
use PHPUnit\Framework\TestCase;


/**
 * Class CryptographerTest
 *
 * @package Test\Unit\Core\Security
 */
class CryptographerTest extends TestCase
{
    public function testCreateEmailKeySaltReturnsCorrectLengthInteger()
    {
        // exercise
	    $cryptographer = new Cryptographer();
        $salt = $cryptographer->createSalt(10);

        // make assertions
        $this->assertEquals(10, strlen($salt), 'unexpected number of digits');
    }

    public function testCreateEmailKeySaltDefaultsToCorrectLengthWhenNotSupplied()
    {
        // exercise SUT
	    $cryptographer = new Cryptographer();
	    $salt = $cryptographer->createSalt();

        // make assertions
        $this->assertEquals(16, strlen($salt), 'unexpected number of digits');
    }

    public function testVerifyEmailKeyReturnsTrueOnSuccess()
    {
        // set up fixtures
	    $cryptographer = new Cryptographer();
	    $salt = $cryptographer->createSalt(16);
        $hash = $cryptographer->createHash($salt);

        //exercise SUT
        $compare = $cryptographer->verify($hash, $salt);

        // make assertions
        $this->assertTrue($compare, 'failed to verify hash');
    }
}