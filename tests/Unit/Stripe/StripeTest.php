<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/11/19
 * Time: 12:42 PM
 */

namespace Test\Unit\Stripe;

use PapaLocal\Stripe\Stripe;
use PHPUnit\Framework\TestCase;

/**
 * Class StripeTest
 * @package Test\Unit\Stripe
 */
class StripeTest extends TestCase
{
    /**
     *-----------------------
     *  Account Management
     *---------------------
     */

    public function testCanCreateAccount()
    {
        $this->markTestIncomplete();

        $stripe = new Stripe();
        $stripe->createAccount();
    }

    public function testCanRetrieveAccountDetails()
    {
        $this->markTestIncomplete();

        $stripe = new Stripe();
        $stripe->retrieveAccountDetails();
    }

    public function testCanUpdateAccount()
    {
        $this->markTestIncomplete();

        $stripe = new Stripe();
        $stripe->updateAccount();
    }

    public function testCanDeleteAccount()
    {
        $this->markTestIncomplete();

        $stripe = new Stripe();
        $stripe->deleteAccount();
    }
}