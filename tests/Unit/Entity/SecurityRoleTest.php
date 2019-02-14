<?php

/**
 * Created by PhpStorm.
 * User: Yacouba Keita
 * Date: 11/30/17
 * Time: 10:52 AM
 */

namespace Test\Unit\Entity;

use PHPUnit\Framework\TestCase;
use PapaLocal\Entity\SecurityRole;

class SecurityRoleTest extends TestCase
{
    public function testCanInstantiate()
    {
        $securityRole = new SecurityRole();
        $this->assertInstanceOf(SecurityRole::class, $securityRole);
    }
}