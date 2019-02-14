<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/4/18
 * Time: 5:24 PM
 */

namespace PapaLocal\Response;

use PapaLocal\Data\Ewebify;

/**
 * RestResponseMessage.
 *
 * Response messages for use in RestApi
 */
class RestResponseMessage
{
    /**
     * User Registration
     */
    const USERNAME_IN_USE = 'The username you provided seems to be unavailable. Please try another email address, or contact us to unlock your account.';

    const INTERNAL_SERVER_ERROR = 'It looks like this portion of our site is not working properly. Our tech team has been notified and will begin working on a solution soon.';
    const USER_NOT_FOUND = 'Looks like we can\'t find that email address in our system. If you don\'t have an account, try registering for one.';
    const PASSWORD_RESET_EMAIL_FAILED = 'We were unable to send an email to the address provided. Please give us a call.'
    . ' at ' . Ewebify::CUST_SUPP_PHONE . ' to unlock your account.';
    const PASSWORD_RESET_EMAIL_SUCCESS = 'We\'ve sent you an e-mail with a link to unlock your account.';

    /**
     * Billing/Transactions
     */
    const ADD_CARD_BAD_NUMBER = 'Credit card number is invalid';
    const ADD_CARD_DUPLICATE = 'That card number is already attached to your account.';
    const CHARGE_ACCOUNT_FAIL = 'Unfortunately, there was a problem using that account. We are looking into the issue. In the meantime, you can try to use another account.';
    const DUPLICATE_TRANSACTION = 'To protect your account against fraud, we ask that you wait before submitting another deposit request for the same amount.';

}
