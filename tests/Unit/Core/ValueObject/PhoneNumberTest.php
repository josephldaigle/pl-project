<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 10/1/18
 * Time: 10:07 AM
 */


namespace Test\Unit\Core\ValueObject;


use PapaLocal\Core\ValueObject\PhoneNumber;
use PHPUnit\Framework\TestCase;


/**
 * Class PhoneNumberTest
 *
 * @package Test\Unit\Core\ValueObject
 */
class PhoneNumberTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testCannotInstantiateWhenInvalidPhoneType()
    {
        $phoneNumber = new PhoneNumber('5555555555', 'bad type');
    }
}