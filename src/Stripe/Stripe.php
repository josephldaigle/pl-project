<?php
/**
 * Created by PhpStorm.
 * User: achats1992
 * Date: 1/11/19
 * Time: 12:02 PM
 */

namespace PapaLocal\Stripe;


/**
 * Class Stripe
 * @package PapaLocal\Stripe
 */
class Stripe
{
    /**
     *-----------------------
     *  Account Management
     *---------------------
     */

    public function createAccount()
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        $acct = \Stripe\Account::create([
            "type" => "custom",
            "country" => "US",
            "email" => "lgroom@papalocal.com",
            "legal_entity" => array(
                "address" => array(
                    "city"=> "Peachtree City",
                      "country" => "US",
                      "line1" => "205 Pebblestump Pt",
                      "line2" => null,
                      "postal_code" => "30269",
                      "state" => "GA"
                ),
//                "business_name" => 'Heartstar Boutique',
//                "business_tax_id" => "",
                "dob" => array(
                    "day" => 5,
                    "month" => 4,
                    "year" => 1987
                ),
                "first_name" => "Lois",
                "last_name" => "Groom",
                "personal_address" => array(
                    "city"=> "Peachtree City",
                    "country" => "US",
                    "line1" => "205 Pebblestump Pt",
                    "line2" => null,
                    "postal_code" => "30269",
                    "state" => "GA"
                ),
//                "personal_id_number" => true,
                "ssn_last_4" => '8756',
                "type"=> "individual",
            ),
            "tos_acceptance" => array(
                "date" => time(),
                "ip" => "54.187.205.235"
            )
//            "external_accounts" => array(
//                "id" => "ba_1DsZhpC52y8PzNSDlaPGo291",
//                "object" => "bank_account",
//                "account" => "acct_16JQCtC52y8PzNSD",
//                "account_holder_name" => "Lois Groom",
//                "account_holder_type" => "individual",
//                "bank_name" => "STRIPE TEST BANK",
//                "country" => "US",
//                "currency" => "usd",
//                "default_for_currency" => false,
//                "fingerprint" => "1JWtPxqbdX5Gamtz",
//                "last4" => "6789",
//                "metadata" => array(),
//                "routing_number" => "110000000",
//                "status" => "new"
//            )
        ]);

        dump($acct);
    }

    public function createAccountWithToken($token)
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        $acct = \Stripe\Account::create([
            "type" => "custom",
            "country" => "US",
            "account_token" => $token
        ]);

        dump($acct);
    }

    public function retrieveAccountDetails()
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        \Stripe\Account::retrieve("acct_1DrUi2K6iSsmWUFP");
    }

    public function updateAccount()
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        $account = \Stripe\Account::retrieve("acct_1DrUi2K6iSsmWUFP");
        $account->support_phone = "555-867-5309";
        $account->save();
    }

    public function deleteAccount()
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        \Stripe\Account::retrieve("acct_1DsYu9L9JVBDX9Hp")->delete();

    }

    public function addBankAccount($token, $acctId)
    {
        \Stripe\Stripe::setApiKey("sk_test_qe9QQJuZEDKiyeFychtfrBZd");

        $account = \Stripe\Account::retrieve($acctId);
        $account->account_token = $token;
        $account->save();

    }
}