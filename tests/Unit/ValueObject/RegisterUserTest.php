<?php

/**
 * Created by PhpStorm.
 * Date: 2/7/18
 * Time: 1:57 PM
 */

namespace Test\Unit\ValueObject\Form;

use PHPUnit\Framework\TestCase;
use PapaLocal\ValueObject\Form\RegisterUser;

/**
 * @deprecated since v1.0
 * Class RegisterUserValidationTest
 */
class RegisterUserValidationTest extends TestCase
{
    public function testCanCreateRegisterUser()
    {
        $testRegisterUser = new RegisterUser();
        $this->assertInstanceOf(RegisterUser::class, $testRegisterUser);
    }
}