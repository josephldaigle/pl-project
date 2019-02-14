/**
 * Created by joe on 1/3/19.
 */
/**
 * Create a stripe client
 *
 * TODO: Replace this test key with an environment-specific solution. This will need to be
 *
 */



/**
 * Run stripe ui elements
 */
define(['jquery', '/js/lib/modules/app.js', '//js.stripe.com/v3/?noext'], function($, PAPALOCAL, stripe) {
    // Create an instance of Elements.
    let stripeClient = Stripe('pk_test_QvDIj8nc2kD5VtAoHLt1tp8k');
    let elements = stripeClient.elements();

    /**
     * Adds a Stripe token to the form's request data.
     * @param event
     * @returns {Promise<void>}
     */
    let createToken = function(event) {

        var tokenId = stripeClient.createToken('bank_account', {
            country: 'US',
            currency: 'usd',
            routing_number: event.formData.routingNumber,
            account_number: event.formData.accountNumber,
            account_holder_name: event.formData.firstName + ' ' + event.formData.lastName,
            account_holder_type: 'individual'

        }).then(function(result) {
            if (result.error) {
                console.error('Unable to get token.', result);
                return undefined;

            } else {
                console.log('token id: ' + result.token.id);
                return result.token.id;
            }
        });

        console.log('token ', tokenId);

    };

    let style = {
        base: {
            color: '#32325d',
            // lineHeight: '18px',
            // fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    /**
     * Toggle the terms checkbox (enable/disable the submit button on addBankAccount form
     *
     *
     */
    let checkTermsBox = function() {
        if ($('form[name="addBankAccount"]').find('input[name="terms"]').is(':checked')) {
            $('form[name="addBankAccount"]').find('input[type="submit"]').attr('disabled', false);
        } else {
            $('form[name="addBankAccount"]').find('input[type="submit"]').attr('disabled', true);
        }
    };

    let classes = {
        base: 'form-control clear-border-radius'
    };

    // Create an instance of the card Element.
    // let card = elements.create('card', {style: style, classes: classes});
    //
    // card.addEventListener('change', function(event) {
    //     var alertContainer = $('form[name="addBankAccount"]').find('#alert-container');
    //
    //     if (event.error) {
    //         $(alertContainer).html('<p>' + event.error.message + '</p>');
    //         alertContainer.textContent = event.error.message;
    //         alertContainer.addClass('alert');
    //         alertContainer.addClass('alert-danger');
    //
    //     }
    // });

    // Add an instance of the card Element into the `card-element` <div>.
    // card.mount($('form[name="addBankAccount"]').find('div[data-stripe="card-number"]')[0]);

    return {checkTermsBox, createToken}
});



