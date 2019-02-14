<?php
/**
 * Created by eWebify, LLC.
 * Creator: joedaigle
 * Date: 12/15/17
 */

namespace PapaLocal\AuthorizeDotNet;

use net\authorize\api\contract\v1\CreateCustomerPaymentProfileResponse;
use net\authorize\api\contract\v1\CreateCustomerProfileResponse;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerPaymentProfileType;
use net\authorize\api\contract\v1\CustomerProfileType;
use net\authorize\api\contract\v1\PaymentProfileType;
use net\authorize\api\contract\v1\PaymentType;

/**
 * Interface AuthorizeDotNetInterface.
 *
 * Describes the Authorize.Net API.
 */
interface AuthorizeDotNetInterface extends CustomerProfileInterface, PaymentMethodInterface,
    PaymentTransactionInterface
{

}