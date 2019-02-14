/**
 * Created by joe on 1/6/19.
 */

define(['jquery', 'jqueryMask', 'bootstrap', 'bootstrapSelect', 'bootstrapMoment'], function($, jqueryMask, bootstrap, bootstrapSelect, bootstrapMoment) {
    "use strict";

    let PAPALOCAL = {
        // base url for the site (environment friendly)
        baseUrl: window.location.protocol + "//" + window.location.host + "/",
        formatter: function() {
            "use strict";
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            });
        },
        initScripts: [],
        initialize: function() {
            for (var key in PAPALOCAL.initScripts) {
                if (PAPALOCAL.initScripts[key] !== null) {
                    PAPALOCAL.initScripts[key]();
                }
            }

            PAPALOCAL.attachEventHandlers();
        },
        /**
         * Functions that attach to UI elements to handle user interactions.
         */
        spinner: {
            /**
             * Activate the spinner to wait for the specified milliseconds.
             *
             * @param milliseconds
             */
            setTimer: function (milliseconds) {
                $('#spinner').modal({
                    backdrop: 'static',
                    keyboard: false,
                    focus: true,
                    show: true
                });

                window.setTimeout(
                    function() {
                        $('#spinner').modal('hide');
                    }, milliseconds
                )
            },
            /**
             * Activate the spinner indefinitely.
             */
            open: function () {
                $('#spinner').modal({
                    backdrop: 'static',
                    keyboard: false,
                    focus: true,
                    show: true
                }).css("z-index", "1701");
                $('.modal-backdrop.in').css("z-index", "1700");
            },
            /**
             * Close the spinner.
             */
            close: function () {
                $('#spinner').modal('hide').css("z-index", "");
                $('.modal-backdrop.in').css("z-index", "");
                return;
            }
        },
        /**
         * Functions that attach to UI elements to handle user interactions.
         * These are available for use in PAPALOCAL.attachEventHandlers().
         */
        eventHandlers: {
            /**
             * Handles opening the referralAgreementInvitee modal from the feed (clicking
             * on addParticipant button on a referral agreement.)
             */
            inviteParticipant: function (event) {
                // add the agreementId field, if not present
                try {
                    let form = $('form[name="addReferralAgreementInvitee"]');

                    // clear the form of any existing date
                    PAPALOCAL.last = form;
                    PAPALOCAL.form.clear(form);

                    // add the agreementId to the form
                    if ($(form).find(':input[name="agreementId"]').length < 1) {
                        $(form).append('<input type="hidden" name="agreementId" value="' + $(event.currentTarget).data('agreementid') + '" class="form-control" />');
                    }

                    /**
                     * add contextual detail, so the application can correctly handle the request
                     *
                     * this stems from the fact that the app and front-end must communicate when the user's
                     * intent is, so that the proper form-handling process is utilized in the UI and back-end.
                     */
                    if ($(form).find(':input[name="context"]').length < 1) {
                        $(form).append('<input type="hidden" name="context" value="addInviteeFromFeed" class="form-control" />');
                    }

                    // open modal
                    $('#referralAgreementInvitee').modal();

                    // hide the proceed button
                    $(form).find(':input[name="proceed"]').hide();

                    return;

                } catch (error) {
                    console.log(error);
                }
            },
            /**
             * Handler for addFunds button, found on a referral agreement in the feed.
             *
             * @param event
             */
            addFundsToPublishAgreement: function (event) {
                try {
                    // get ref to the form
                    let form = $('form[name="addFunds"]');

                    // add the agreementId to the form, if not present
                    if ($(form).find(':input[name="agreementId"]').length < 1) {
                        $(form).append('<input type="hidden" name="agreementId" value="' + $(event.currentTarget).data('agreementid') + '" class="form-control" />');
                    }

                    /**
                     * add contextual detail, so the application can correctly handle the request
                     *
                     * this stems from the fact that the app and front-end must communicate when the user's
                     * intent is, so that the proper form-handling process is utilized in the UI and back-end.
                     */
                    if ($(form).find(':input[name="context"]').length < 1) {
                        $(form).append('<input type="hidden" name="context" value="addFundsFromFeedForReferralAgreement" class="form-control" />');
                    }

                    return;

                } catch (error) {
                    console.log(error);
                }
            },
            /**
             * Remove context and agreementId from addFunds form when modal is closed.
             *
             * This ensures the user can make a deposit from the account profile.
             */
            closeAddFundsForm: function (event) {
                let form = $(event.currentTarget).find('form[name="addFunds"]');

                // remove context
                if ($(form).find(':input[name="context"]').length > 0) {
                    $(form).find(':input[name="context"]').remove();
                }

                // remove agreementId
                if ($(form).find(':input[name="agreementId"]').length > 0) {
                    $(form).find(':input[name="agreementId"]').remove();
                }
            },
            /**
             * Toggle the delivery method form on addReferral form.
             */
            referralDestinationToggle: function(event) {

                let selectedValue = $(event.currentTarget).val();

                if (selectedValue == 'agreement') {
                    $('#sendToAgreement').removeClass('hide');
                    $('#sendToContact').addClass('hide');
                }

                if (selectedValue == 'contact') {
                    $('#sendToAgreement').addClass('hide');
                    $('#sendToContact').removeClass('hide');
                }

            },

            /**
             * Notification icon on app header
             */
            hideNotificationCounter: function() {
                $("#notification-counter").addClass('hide');
            },

            /**
             * TextArea input field character counter
             */
            countChar: function (event) {

                var max = 255; //$(event.currentTarget).attr('maxlength');
                var len = event.currentTarget.value.length;
                if (len >= max) {
                    event.value = event.value.substring(0, max);
                } else {
                    $(event.currentTarget).siblings('span.help-block').text((max - len) + ' ' + 'Remaining');
                }
            },
            /**
             * Edit button for account profile.
             */
            profileEditButton: function(event) {

                // fetch reference to the form
                var form = $(event.currentTarget).closest('.form-group');

                // call toggle form mode
                PAPALOCAL.form.toggleFormMode(form);

                // set the data-orig attr on the forms inputs for cancelling
                // only applies to non-submit and non-collection inputs
                $($(form).find(':input.form-control:not([type="submit"]):not(.collection-input)')).each(function(k, e) {

                    // set the input element's value back to it's original state
                    if ($(e).is('select')) {
                        // input is select type
                        $(e).attr('data-orig', $(e).find(':selected').val());
                    } else {
                        // input is other
                        $(e).attr('data-orig', $(e).val());
                    }

                    // remove any help texts
                    $(e).siblings('span.help-block').text('');
                });


                $($(form).find(':input.primary-collection-input')).each(function(k,e) {
                    // copy the form row
                    var row = $(e).closest('.form-group').clone(true);
                    $(row).addClass('hidden');

                    // add row to form group
                    $(e).closest('.form-group').parent().append(row);
                });
            },
            /**
             * Cancel button for account profile.
             */
            profileCancelButton: function(event) {
                event.preventDefault();

                if ($(event.currentTarget).closest('form').attr('name') == 'rateReferral') {
                    $('#referral-rate').rating('reset').rating('refresh', {disabled: true});
                    $('.referral-feedback').addClass('hide');
                }

                // fetch reference to form
                var form = $(this).closest('form');

                if ($(form).closest('.modal').length == 0
                    && $(form).find('.primary-collection-input').length > 0) {
                    // form is collection, not on modal

                    // unhide original form
                    var originalForm = $(form).siblings('.hidden');
                    $(originalForm).removeClass('hidden');

                    // remove edited form
                    $(originalForm).siblings().remove();

                    // set form ref to new form
                    form = $(originalForm);
                }

                PAPALOCAL.form.toggleFormMode(form);

                // reset values for form inputs
                $($(form).find(':input.form-control:not([type="submit"]):not(.collection-input)')).each(function(k, e) {
                    // set the input element's value back to it's original state
                    if ($(e).is('select')) {
                        // console.log('TODO: handle select element');
                        if ($(e).val() !== $(e).attr('data-orig')) {
                            $(e).val($(e).attr('data-orig')).prop('selected', true);
                        }
                    } else {
                        $(e).val($(e).attr('data-orig'));
                    }

                    $(e).siblings('span.help-block').text('');
                });

                // clear form alerts
                PAPALOCAL.form.clearAlerts();
            },
            toggleFormEditMode: function(event) {
                event.preventDefault();
                PAPALOCAL.eventHandlers.profileEditButton(event);
                PAPALOCAL.attachEventHandlers();

                $('textarea.about-text + span.help-block').removeClass('hide');

                if ($(event.currentTarget).closest('form').attr('name') == 'rateReferral') {
                    $('#referral-rate').rating('refresh', {disabled: false});
                    $('.referral-feedback').removeClass('hide');
                }
            },
            /**
             * Submit action for all AJAX forms (data-url property on form tag)
             */
            submitAjaxForm: function(event) {
                event.preventDefault();

                PAPALOCAL.form.submit(event);
            },
            /**
             * Load user's agreements on addReferral form modal.open event
             */
            loadReferralFormAgreements: function() {
                let form = $('form[name="addReferral"]');

                $.get({
                    url: PAPALOCAL.baseUrl + '/agreement/participant/agreements/all',
                    dataType: "html",
                    success: function(data, textStatus,jqXHR) {
                        // replace options
                        if (data.length > 1) {
                            $(form).find('select[name="selectAgreement"]').replaceWith(data);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        PAPALOCAL.log('error', 'Form ' + 'addReferral submission failed as ' + JSON.stringify($(form).serializeArray()));

                        $('form[name="addReferral"]').find('select[name="selectAgreement"]').replaceWith("<option value=\"-1\">None Available</option>");
                    }
                });
            }
        },
        /**
         * Attaches application event handlers to ui elements
         */
        attachEventHandlers: function() {
            /**
             * Invite participant to referral agreement
             */
            $('a.btn-invite-participant').off('click').on('click', PAPALOCAL.eventHandlers.inviteParticipant);
            /**
             * Open addFund form for referral agreement from feed.
             */
            $('a.btn-add-funds').off('click').on('click', PAPALOCAL.eventHandlers.addFundsToPublishAgreement);
            /**
             * Remove context and agreementId when addFunds modal form is closed.
             */
            $('#funds').on('hidden.bs.modal', PAPALOCAL.eventHandlers.closeAddFundsForm);
            /**
             * Load user's agreements into the addReferral form.
             */
            $('#addNewReferral').off('shown.bs.modal').on('shown.bs.modal', PAPALOCAL.eventHandlers.loadReferralFormAgreements);

            /**
             * Toggle between options where user would like to send the referral.
             */
            $('select[name=referralDestination]').change(PAPALOCAL.eventHandlers.referralDestinationToggle);

            /**
             * Count number of characters in textarea input macro.
             */
            $('.about-text').keyup(PAPALOCAL.eventHandlers.countChar);

            /**
             * Edit button for account profile line-items.
             */
            $('.btn-edit').off('click').on('click', PAPALOCAL.eventHandlers.toggleFormEditMode);

            /**
             * Cancel button for account profile line-items.
             */
            $('.btn-cancel').off('click').on('click', PAPALOCAL.eventHandlers.profileCancelButton);

            /**
             * Hides notification counter when notification icon is clicked
             */
            $("#notification-icon").off('click').on('click', PAPALOCAL.eventHandlers.hideNotificationCounter);

            /**
             * Attach add/remove buttons to forms with collection style inputs.
             */
            $('form[name="createReferralAgreement"], form[name="updateReferralAgreementLocation"], form[name="updateReferralAgreementService"]').find('.collection-input-add-btn').off('click').on('click', PAPALOCAL.form.input.textCollection.add);

            /**
             * Prevent form submission on 'Return' keypress.
             * Without this, the services and locations widget on referral agreements would not work
             * and forms would not submit properly.
             */
            $("input.form-control").keydown(function(event) {
                if (event.keyCode === 13) {
                    return false;
                }
            });

            /**
             * Display char count on keypress.
             */
            $('textarea[name="reviewerNote"]').keydown(function(event) {
                $('textarea[name="reviewerNote"] + span.help-block').removeClass('hide');
            });

            /**
             * Handle 'Return' keypress for add button on forms with collection-style inputs.
             */
            $('form').filter(function() { return $(this).find('div.collection-input-table-display').length > 0; }).each(function(){
                $(this).find('input.collection-input').off('keyup').on('keyup', function (event) {

                    // Number 13 is the "Enter" key on the keyboard
                    if (event.keyCode === 13) {

                        // trigger the button element with a click
                        $(this).parent().siblings().find('i.collection-input-add-btn').click();

                        return false;
                    }
                });
            });

            /**
             * Attach remove participant handler to feed display on referral agreement.
             */
            $('i.remove-participant').each(function() {
                $(this).off('click').on('click', function(event) {
                    PAPALOCAL.spinner.open();

                    let requestData = {
                        inviteeGuid: $(this).data('invitee-guid'),
                        agreementGuid: $(this).data('agreement-guid'),
                        _csrf_token: $(this).data('csrf'),
                    };

                    $.ajax({
                        headers: {
                            Accept: "application/json; charset=utf-8"
                        },
                        type: 'POST',
                        contentType: 'application/json',
                        url: PAPALOCAL.baseUrl + '/agreement/invitee/remove',
                        data: JSON.stringify(requestData),
                        dataType: "json",
                        success: function (data, textStatus, jqXHR) {

                            // close the spinner
                            PAPALOCAL.spinner.close();

                            // show user alert
                            PAPALOCAL.form.alert()
                            PAPALOCAL.form.alert('Participant has been removed.', 'success');

                            // delete remove button and replace with pill-box
                            let label = $(event.currentTarget).closest('.row').find('.invitee-status-label');
                            $(label).parent().html('<span class="label label-danger invitee-status-label"></span>');
                            $(label).html('<span class="label label-danger invitee-status-label">Removed</span>');
                            $(event.currentTarget).remove();


                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            PAPALOCAL.spinner.close();


                            // Show user a default message (system message not provided).
                            PAPALOCAL.alert(PAPALOCAL.messages.actionFailed.toString(), 'danger');
                        }
                    });
                });
            });

            /**
             * Submit all ajax forms to PAPALOCAL.form.submit()
             *
             * This function exists because forms do not submit automatically in Bootstrap Modals.
             */
            $('form').each(function() {
                $(this).on('submit', function() {
                    $(this).find(':input.form-control:not([type="submit"])').each(function() {
                        $(this).unmask();
                    });
                });

                // ajax forms only (has data-url attr)
                if ($(this).data('url')) {

                    // set submit handler on form
                    $(this).off('submit').on('submit', PAPALOCAL.eventHandlers.submitAjaxForm);

                    var form = $(this);

                    $(this).find('input:submit').off('click').on('click', { 'form': form }, function(event) {
                        event.preventDefault();

                        // add inputs to referral agreement invitee form
                        if ($(this).attr('name') == 'saveAddMore') {
                            // user clicked 'Save + New Invitee'
                            // remove flags for discard & isLast
                            if ($(form).find(':input[name="isLast"]').length > 0) {
                                $(form).find(':input[name="isLast"]').remove();
                            }
                            if ($(form).find(':input[name="discardContinue"]').length > 0) {
                                $(form).find(':input[name="discardContinue"]').remove();
                            }
                        }

                        // user clicked 'Save and Continue'
                        if ($(this).attr('name') == 'saveContinue') {
                            if ($(form).find(':input[name="isLast"]').length < 1) {
                                $(form).append('<input type="hidden" name="isLast" value="true" class="form-control" />');
                            }
                            if ($(form).find(':input[name="discardContinue"]').length > 0) {
                                $(form).find(':input[name="discardContinue"]').remove();
                            }
                        }

                        // user clicked 'Proceed without Saving'
                        if ($(this).attr('name') == 'proceed') {
                            if ($(form).find(':input[name="isLast"]').length < 1) {
                                $(form).append('<input type="hidden" name="isLast" value="true" class="form-control" />');
                            }
                            if ($(form).find(':input[name="discardContinue"]').length < 1) {
                                $(form).append('<input type="hidden" name="discardContinue" value="true" class="form-control" />');
                            }
                        }

                        // user clicked 'Approve Request' on referral dispute.
                        if ($(this).attr('name') == 'approve') {
                            $(this).closest('form').find(':input[name="resolution"]').val('approved');
                        }

                        // user clicked 'Deny Request' on referral dispute.
                        if ($(this).attr('name') == 'deny') {
                            $(this).closest('form').find(':input[name="resolution"]').val('denied');
                        }

                        // submit the form
                        $(event.data.form).submit();
                    });
                }
            });
        },
        // functions for handling form submissions
        form: {
            /**
             * Serialize a form's html data into json for submission via AJAX call to back-end.
             * Passes form data to dataHandlers for modification.
             *
             * @param form
             * @returns {*}
             */
            serialize: function(form) {

                // unmask any masked fields
                $(form).find(':input.form-control:not([type="submit"])').each(function() {
                    $(this).unmask();
                });

                /**
                 * These functions handle forms with collection-style inputs
                 *
                 * TODO: Move to some common structure
                 * @type {*}
                 */
                var hasTables = $(form).find('div.collection-input-table-display');
                if (hasTables) {
                    // Find disabled inputs, and remove the "disabled" attribute
                    $(form).find('div.collection-input-table-display').each(function(index, el) {
                        $(el).children().find(':input').each(function(index, el) {
                            $(this).removeAttr('disabled');
                        });
                    });
                }

                var postData = {};
                var rawFormData = $(form).serializeArray();

                // disable collection inputs again
                if (hasTables) {
                    // re-disable the set of inputs
                    $(form).find('div.collection-input-table-display').each(function(index, el) {
                        $(el).children().find(':input').each(function(index, el) {
                            $(this).attr('disabled', 'disabled');
                        });
                    });
                }

                // convert array fields to json
                for (var i = 0; i < rawFormData.length; i++) {

                    var name = rawFormData[i]['name'];
                    var value = rawFormData[i]['value'];

                    if (value != '') {
                        if (name.substr(name.length - 2, name.length) == '[]') {
                            var reducedName = name.substr(0, name.length - 2);

                            if (postData[reducedName] == undefined) {
                                postData[reducedName] = [value];
                            } else {
                                postData[reducedName].push(value);
                            }
                        } else {
                            postData[name] = value;
                        }
                    }
                }

                return PAPALOCAL.form.dataHandler.handle($(form).attr('name'), postData);
            },
            //provide special data preparation for specific forms
            dataHandler: {
                handlers: {
                    userRegistration: function(data) {
                        var requestData = {
                            firstName: data.firstName,
                            lastName: data.lastName,
                            username: data.email,
                            password: data.password,
                            confirmPassword: data.passwordConfirm,
                            phoneNumber: (data.phoneNumber) ? data.phoneNumber.replace(/[^0-9]/g, '') : '',
                        };

                        if (data.businessOwner === 'businessOwner') {
                            requestData.businessOwner = 'true';
                            requestData.companyName = data.companyName;
                            requestData.companyEmailAddress = data.companyEmailAddress;
                            requestData.companyPhoneNumber = (data.companyPhoneNumber) ? data.companyPhoneNumber.replace(/[^0-9]/g, '') : '';

                            requestData.companyAddress = (data.selectAddress) ? requestData.companyAddress = PAPALOCAL.form.dataHandler.parseAddressField(data) : [];
                        }

                        return requestData;
                    },
                    addCreditCard: function(data) {

                        var parsedAddress = null;

                        if (data.selectAddress > 0) {
                            parsedAddress = {
                                id: data.selectAddress
                            };
                        } else {
                            // submit full address
                            parsedAddress = PAPALOCAL.form.dataHandler.parseAddressField(data);
                        }

                        var requestData = {
                            firstName: data.firstName,
                            lastName: data.lastName,
                            cardNumber: data.cardNumber,
                            cardType: data.cardType,
                            securityCode: data.securityCode,
                            expirationMonth: data.expirationMonth,
                            expirationYear: data.expirationYear,
                            isDefaultPayMethod: data.isDefaultPayMethod
                        };
                        if (data.agreementId) {
                            requestData.agreementId = data.agreementId;
                        }

                        if (parsedAddress !==  null) {
                            $.extend(requestData, parsedAddress);
                        }

                        return requestData;
                    },
                    addBankAccount: function(data) {
                        return data;
                    },
                    addFunds: function(data) {
                        let requestData = {
                            accountId: data.accountId,
                            accountType: 'Credit Card',
                            agreementId: data.agreementId,
                            context: data.context
                        };

                        if (data.amount > 0) {
                            requestData.amount = data.amount + '.00';
                        } else {
                            requestData.amount = '0.00';
                        }

                        return requestData;
                    },
                    // withdrawFunds: function(data) {
                    //     let requestData = {
                    //         amount: data.amount + '.00',
                    //         userGuid: data.userGuid
                    //     };
                    //
                    //     return requestData;
                    // },
                    accountProfileUsername: function(data) {
                        return {
                            emailAddress: data.username
                        };
                    },
                    accountProfileAddress: function(data) {
                        // submit full address
                        var addr = PAPALOCAL.form.dataHandler.parseAddressField(data)

                        if (data.guid) {
                            $.extend(addr, {guid: data.guid});
                        }

                        if (data.type) {
                            $.extend(addr, {type: data.type});
                        }

                        return addr;

                    },
                    companyProfileAddress: function(data) {

                        var addr = PAPALOCAL.form.dataHandler.parseAddressField(data);

                        if (data.guid) {
                            $.extend(addr, {guid: data.guid});
                        }

                        if (data.type) {
                            $.extend(addr, {type: data.type});
                        }

                        return addr;

                    },
                    accountProfileWebsite: function(data) {
                        return {
                            website: data.protocol + (data.website == 'undefined') ? '' : data.website,
                            guid: data.guid
                        };
                    },
                    addCompany: function(data) {
                        let requestData = {
                            name: data.companyName,
                            phoneNumber: (data.phoneNumber) ? data.phoneNumber.replace(/[^0-9]/g, '') : '',
                            emailAddress: data.email
                        };

                        if (data.selectAddress) {
                            // submit full address
                            requestData.address = PAPALOCAL.form.dataHandler.parseAddressField(data);
                        } else {
                            requestData.address = '';
                        }

                        return requestData;
                    },
                    createReferralAgreement: function(data) {
                        data.includedLocations = data.includeLocation;
                        delete data.includeLocation;

                        data.excludedLocations = data.excludeLocation;
                        delete data.excludeLocation;

                        data.includedServices = data.includeService;
                        delete data.includeService;

                        data.excludedServices = data.excludeService;
                        delete data.excludeService;

                        return data;
                    },
                    updateReferralAgreementLocation: function(data) {
                        let requestData = {
                            agreementGuid: data.agreementGuid,
                            locations: (data.includeLocation) ? data.includeLocation : data.excludeLocation
                        };

                        return requestData;
                    },
                    addReferral: function(data) {
                        // only submit contact or agreement, not both.
                        var requestData = {
                            firstName: data.firstName,
                            lastName: data.lastName,
                            emailAddress: data.emailAddress,
                            phoneNumber: data.phoneNumber,
                            address: PAPALOCAL.form.dataHandler.parseAddressField(data),
                            about: data.referralAbout,
                            note: data.referralNote,
                        };

                        if (data.referralDestination == 'agreement') {
                            requestData.agreementId = data.selectAgreement;
                        } else if (data.referralDestination == 'contact') {
                            requestData.recipientFirstName = data.recipientFirstName;
                            requestData.recipientLastName = data.recipientLastName;
                            requestData.recipientPhoneNumber = data.recipientPhoneNumber;
                            requestData.recipientEmailAddress = data.recipientEmail;
                        }

                        return requestData;
                    },
                    resolveDispute: function (data) {

                        let requestData = {
                            referralGuid: data.referralGuid,
                            resolution: data.resolution,
                            reviewerNote: data.reviewerNote,
                        };

                        return requestData;
                    }
                },
                handle: function(formName, data) {
                    if (PAPALOCAL.form.dataHandler.handlers.hasOwnProperty(formName)) {
                        return PAPALOCAL.form.dataHandler.handlers[formName](data);
                    } else {
                        return data;
                    }
                },
                /**
                 * Extract the address from a request data object.
                 * Should only be used inside PAPALOCAL.form.dataHandlers
                 *
                 * @param data
                 * @returns {*}
                 */
                parseAddressField: function(data) {
                    if (typeof data.selectAddress !== undefined && data.selectAddress !== '' && data.selectAddress !== null) {
                        // data contains an address, parse it
                        return {
                            streetAddress: data.street_number + ' ' + data.route,
                            city: data.locality,
                            state: data.administrative_area_level_1,
                            postalCode: data.postal_code,
                            country: data.country
                        };
                    } else {
                        return {
                            streetAddress: '',
                            city: '',
                            state: '',
                            postalCode: '',
                            country: ''
                        };
                    }
                }
            },
            /**
             * Finishers are special handlers that set the UI to an acceptable state
             * after a form is submitted.
             */
            finisher: {
                userRegistration: function(form, data) {
                    $('#companyOwnerForm').addClass('hidden');
                },
                accountProfileUsername: function(form, data) {
                    window.location.href='#userPanel';
                    window.location.reload(true);
                },
                accountProfilePassword: function(form, data) {
                    window.location.href='#userPanel';
                    window.location.reload(true);
                },
                addCompany: function(form, data) {
                    // reload page after company created
                    window.location.href='#companyPanel';
                    window.location.reload(true);
                },
                deletePaymentMethod: function(form, data) {
                    window.location.href = '#paymentPanel';
                    window.location.reload(true);
                },
                primaryPay: function(form, data) {
                    window.location.href = '#paymentPanel';
                    window.location.reload(true);
                },
                accountProfileAddFund: function(form, data) {
                    window.location.href = '#paymentPanel';
                    window.location.reload(true);
                },
                addCreditCard: function(form, data) {
                    switch(data.nextForm) {
                        case 'addFunds':    // the user is adding funds as part of the 'createAgreement' process

                            // add agreementId to form,
                            $('form[name="addFunds"]').append('<input type="hidden" name="agreementId" value="' + data.agreementId + '" class="form-control" />');

                            // replace select element with new card
                            if ($('form').find('select[name="accountId"]').children(':first-child').val() == -1) {
                                // remove the empty element
                                $('form').find('select[name="accountId"]').children(':first-child').remove();
                            }

                            $('form').find('select[name="accountId"]').append('<option value="' + data.creditCard.id + '">' + data.creditCard.cardType + ' ending with ' + data.creditCard.cardNumber + '</option>');

                            $('form[name="addFunds"]').append('<input type="hidden" name="context" value="addFundsToCreateAgreement" class="form-control" />');

                            $('#funds').modal();

                            break;
                        default:    // the user has added funds from the account profile
                            // handle account profile page context
                            window.location.href = '#paymentPanel';
                            window.location.reload(true);
                            break;
                    }
                    return;
                },
                addFunds: function(form, data) {
                    window.location.reload(true);
                    // remove contextual information from the form, preventing errors when user adds funds from account profile
                    $(form).find(':input[name="agreementId"]').remove();
                    $(form).find(':input[name="context"]').remove();
                },
                accountProfileRecharge: function(form, data) {
                    window.location.href = '#paymentPanel';
                    // window.location.reload(true);

                    let minBalance = (data.minBalance).toFixed(2);
                    let maxBalance = (data.maxBalance).toFixed(2);

                    // fetch selected options
                    let minBalanceSelect = $(form).find('select[name="minBalance"]');
                    let maxBalanceSelect = $(form).find('select[name="maxBalance"]');

                    // remove prev selected
                    minBalanceSelect.find('[selected]').prop('selected', false);
                    maxBalanceSelect.find('[selected]').prop('selected', false);

                    // set new selected options
                    minBalanceSelect.find('option[value="' + minBalance + '"]').prop('selected', true);
                    maxBalanceSelect.find('option[value="' + maxBalance + '"]').prop('selected', true);
                },
                createReferralAgreement: function(form, data) {
                    /**
                     * Adds the agreement id to the invitee form (only occurs when the create referral
                     * agreement form is successfully submitted)
                     */
                    $('form[name="addReferralAgreementInvitee"]').append('<input type="hidden" name="agreementId" value="' + data.agreementId + '" class="form-control" />');

                    // open the addInvitee modal
                    $('#referralAgreementInvitee').modal();

                    $('form[name="addReferralAgreementInvitee"]').find(':input[name="proceed"]').show();
                },
                addReferralAgreementInvitee: function(form, data) {
                    // handle opening next form, so the user can proceed with publishing the agreement
                    switch(data.nextForm) {
                        case 'addReferralAgreementInvitee':
                            // user previously selected 'Save + New Invitee' (will add another invitee)

                            // add the agreementId if not present
                            if ($('form[name="addReferralAgreementInvitee"]').find(':input[name="agreementId"]').length < 1) {
                                $('form[name="addReferralAgreementInvitee"]').append('<input type="hidden" name="agreementId" value="' + data.agreementId + '" class="form-control" />');
                            }

                            // show invitee modal
                            $('#referralAgreementInvitee').modal();

                            break;

                        case 'addPaymentAccount':
                            /**
                             * user previously select 'Save and Continue' or 'Proceed without saving'
                             * and does not have a billing profile or payment method
                             */
                            // add the agreementId if not present
                            if ($('form[name="addCreditCard"]').find(':input[name="agreementId"]').length < 1) {
                                $('form[name="addCreditCard"]').append('<input type="hidden" name="agreementId" value="' + data.agreementId + '" class="form-control" />');
                            }

                            // show addPayMethod modal
                            $('#addCreditCard').modal();

                            break;

                        case 'addFunds':
                            /**
                             * The user is creating a referral agreement from the addMenu and does not
                             * have an adequate balance.
                             */
                            // add the agreementId if not present
                            if ($('form[name="addFunds"]').find(':input[name="agreementId"]').length < 1) {
                                $('form[name="addFunds"]').append('<input type="hidden" name="agreementId" value="' + data.agreementId + '" class="form-control" />');
                            }

                            // add context, so that backend knows how to handle the request
                            if ($('form[name="addFunds"]').find(':input[name="context"]').length < 1) {
                                $('form[name="addFunds"]').append('<input type="hidden" name="context" value="addFundsToCreateAgreement" class="form-control" />');
                            }

                            // show addFunds modal
                            $('#funds').modal();

                            break;
                        default:
                            /**
                             * Reload the page in order to see the new agreement in feed.
                             *
                             * This prevents un-desireable UI state when the response does not
                             * contain an expected 'nextForm' value.
                             */

                            // reload page
                            window.location.reload(true);
                            break;
                    }
                },
                joinAgreement: function(form, data) {
                    // Remove join and decline buttons from agreement details
                    $(form).closest('#accordionAlert').remove();
                },
                declineAgreement: function(form, data) {
                    // Remove feed item
                    var feedCard = $('#feed-item-container').find('.selected-feed-item').parent();
                    var nextItem = $(feedCard).prev();
                    var feedDisplay = $(form).closest('.feed-display');

                    // remove the current agreement (which was declined)
                    $(feedCard).remove();
                    $(feedDisplay).remove();

                    $(nextItem).trigger('click');
                },
                publishAgreement: function(form, data) {
                    // reload feed page, and select published item
                    window.location.reload(true);
                },
                referralAgreementStatus: function(form, data) {
                    window.location.reload(true);
                },
                addReferral: function(form, data) {
                    $('#sendToAgreement').removeClass('hide');
                    $('#sendToContact').addClass('hide');
                },
                rateReferral: function(form, data) {
                    if (data.score < 3) {
                        $('.referral-dispute').removeClass('hide');
                    }
                    $('.referral-rating-button').addClass('hide');
                    $('#referral-rate').rating('update', data.score).rating('refresh', {disabled: true});
                    $('.dispute-user').append(data.name);
                    $('.dispute-date').append(data.currentDate);
                    $('.dispute-explanation').append(data.feedback);
                },
                resolveDispute: function(form, data) {

                    if (data.resolution == 'approved') {
                        $('#accordion-alert').removeClass('alert-warning').addClass('alert-success');
                        $('.btn-resolute').addClass('hide');
                        $('#acc-alert').css("background-color", "#5cb85c");
                        $('#acc-alert>.panel-title>a.collapsed').text( "Dispute: Approved" );
                    }

                    if (data.resolution == 'denied') {
                        $('#accordion-alert').removeClass('alert-warning').addClass('alert-danger');
                        $('.btn-resolute').addClass('hide');
                        $('#acc-alert').css("background-color", "#d9534f");
                        $('#acc-alert>.panel-title>a.collapsed').text( "Dispute: Denied" );
                    }
                }
            },
            // the last form submitted
            last: '',
            // this function resets a form's input elements (does not handle selectpicker)
            clear: function(form) {
                // clear the most recently submitted form
                if (form === 'undefined') {
                    PAPALOCAL.log('error', 'PAPALOCAL.form.clear() called when no last form set.');

                }

                // reset collection inputs
                $(form).find('.collection-input-table-display').empty();

                PAPALOCAL.last[0].reset();
            },
            /**
             * A generic submit function for AJAX forms.
             *
             * This function handles collecting form data, using dataHandlers, submitting the request, and
             * calling any post-form submission handler functions, such as form clearing and closing.
             *
             * @param event
             */
            submit: function(event) {

                // Prevent notification icon click event from sending out Ajax call when there is no new notification
                if($(event.currentTarget).attr('name') == 'setNotificationsSavePoint') {
                    if($('#notification-counter')[0] == undefined) {
                        return;
                    }
                }

                PAPALOCAL.spinner.open();

                // fetch the form
                let form = $(event.currentTarget).closest('form');

                // field has changed - send request
                PAPALOCAL.last = form;
                PAPALOCAL.form.clearAlerts();

                // prepare request data
                let formData = this.serialize(form);

                // add csrf token to payload
                formData['_csrf_token'] = $(form).find('input[name="_csrf_token"]').val();
                let url = form.data('url');

                $.ajax({
                    headers: {
                        Accept: "application/json; charset=utf-8"
                    },
                    type: 'POST',
                    contentType: 'application/json',
                    url: PAPALOCAL.baseUrl + url,
                    data: JSON.stringify(formData),
                    dataType: "json",
                    success: function (data, textStatus, jqXHR) {
                        // Hide charCount on textarea fields
                        $('textarea.about-text + span.help-block').addClass('hide');

                        // close the spinner
                        PAPALOCAL.spinner.close();

                        // finisher
                        PAPALOCAL.form.finish(form, $(form).data('form-type'), data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        PAPALOCAL.spinner.close();

                        /**
                         * When the submit button is clicked on a modal form, the class 'modal-open' is removed from the page
                         * <body> tag, preventing form from scroll.
                         *
                         * This event adds back the 'modal-open' class to the page <body> tag.
                         */
                        $('body').addClass('modal-open');

                        PAPALOCAL.log('error', 'Form ' + $(form).attr('name') + ' submission failed as ' + JSON.stringify(formData));

                        PAPALOCAL.form.clearAlerts();

                        // display flash error
                        if (jqXHR.responseJSON) {

                            // Show user the system generated response message
                            if (jqXHR.responseJSON.validationErrors) {
                                PAPALOCAL.form.alert(form, 'danger', jqXHR.responseJSON.validationErrors);
                            } else {
                                PAPALOCAL.form.alert(form, 'danger', jqXHR.responseJSON.message);
                            }

                        } else {
                            // Show user a default message (system message not provided).
                            PAPALOCAL.form.alert(PAPALOCAL.form.last, 'danger', PAPALOCAL.messages.actionFailed.toString());
                        }

                        // if form is modal - scroll to top so user can see validation errors
                        $(form).closest('.modal.in').scrollTop(0);

                    }
                });
            },
            /**
             * Handles resetting AJAX forms after successful submission.
             *
             * This function
             *
             * @param form
             * @param type
             * @param data
             */
            finish: function(form, type, data) {
                switch (type) {
                    case 'modal':       // forms in modal containers

                        let modal = $(form).closest('.modal');

                        // attach event handler to modal to display alert on page after modal closed.
                        modal.one('hidden.bs.modal', function(e) {
                            // show success alert to user after modal closed
                            PAPALOCAL.alert(data.message, 'success');
                        });

                        // clear the form
                        PAPALOCAL.form.clear($(modal).find('form'));

                        // hide the modal
                        modal.modal('hide');

                        break;
                    case 'standard':    // normal, page-based forms
                        // Prevent alert from showing on notification icon click event
                        if ($(form).attr('name') !== 'setNotificationsSavePoint') {
                            PAPALOCAL.alert(data.message, 'success');
                        }

                        // clear the form
                        PAPALOCAL.form.clear(form);

                        break;
                    case 'inline':      // forms such as account profile, or feed detail column
                        /**
                         * sets data-origin attr on input elements so the forms original value
                         * can be restored when a user cancels edits
                         */
                        let input = $(form).find(':input.form-control:not([type="submit"])');
                        $(input).attr('data-orig', $(input).val());

                        // PAPALOCAL.toggleFieldMode(input, 'hide');
                        PAPALOCAL.form.toggleFormMode(form);

                        // show alert
                        PAPALOCAL.alert(data.message, 'success');

                        break;
                    case 'default':
                        break;
                }

                // call form-specific finisher, if available
                let finisherName = $(form).attr('name');
                if (PAPALOCAL.form.finisher.hasOwnProperty(finisherName)) {
                    PAPALOCAL.form.finisher[finisherName](form, data);
                }
            },
            /**
             * Display a Bootstrap alert on a form.
             * @param form      the form element to prepend the message container to
             * @param level     severity of bootstrap alert (danger, info, success, etc)
             * @param message   string message to display or array of messages. If array, then the function will
             *      assign each value in the array to a <p></p> tag.
             */
            alert: function(form, level, message) {
                // toggle alert container class
                $(form).find('#alert-container').removeClass();
                $(form).find('#alert-container').addClass('alert alert-' + level);

                if (Object.prototype.toString.call(message) === "[object Array]") {
                    message.reverse();
                    $.each(message, function(index, value) {
                        $(form).find('#alert-container').prepend('<p>' + value + '</p>');
                    })
                } else {
                    $(form).find('#alert-container').prepend('<p>' + message + '</p>');
                }
            },
            /**
             * Clear all alerts on all forms.
             */
            clearAlerts: function() {
                $.each($('form').find('#alert-container'), function(index, element) {
                    $(element).empty();
                    $(element).removeClass();
                });
            },
            /**
             * Toggles the form 'edit' mode in account profile.
             *
             * @param form
             */
            toggleFormMode: function (form) {
                // toggle the forms classes
                $(form).toggleClass('view-mode edit-mode');

                // update inputs
                var context = (form.hasClass('view-mode')) ? 'hide' : 'show';

                // toggle inputs to allow editing
                $(form.find(':input.form-control:not([type="submit"])').each(function(key, val){
                    PAPALOCAL.form.toggleFieldMode(val, context);
                }));
            },
            /**
             * Handler for toggling accordion form fields.
             * This function specifically addresses needs for different input types.
             *
             * @param target the elements to toggle (can be array of elements or single element)
             * @param context 'show' or 'hide'
             */
            toggleFieldMode: function(target, context) {
                if (context === 'show') {

                    if ($(target).hasClass('collection-input')) {

                        // display remove buttons
                        $(target).closest('.collection-input-row').find('i.collection-input-del-btn').removeClass('hidden');
                        $(target).closest('.collection-input-row').find('i.collection-input-del-btn').off('click').on('click', PAPALOCAL.form.input.textCollection.remove);

                        if ($(target).hasClass('primary-collection-input')) {
                            $(target).closest('.form-group').find('.collection-input-add-btn').removeClass('hidden');
                            $(target).removeAttr('disabled');
                        }

                    } else {
                        // toggle std input
                        $(target).removeAttr('disabled');
                    }

                    // refresh if element is selectpicker input type
                    if ($(target).hasClass('selectpicker')) {
                        $(target).selectpicker('refresh');
                    }

                } else {
                    // context is 'hide'

                    if ($(target).hasClass('collection-input')) {

                        // hide add and remove buttons on collection inputs
                        $(target).closest('.form-group').find('.collection-input-del-btn').addClass('hidden');
                        $(target).closest('.form-group').find('.collection-input-add-btn').addClass('hidden');

                    } else {

                        // handle selectpicker input type
                        if($(target).hasClass('selectpicker')) {
                            $(target).selectpicker('refresh');
                        }
                    }

                    // disable input element
                    $(target).attr('disabled', true);
                }
            },
            /**
             * Functions for form input elements.
             */
            input: {
                textCollection: {
                    add: function(event) {

                        // fetch ref to main input element
                        var input = $(event.currentTarget).closest('.form-group').find('.primary-collection-input');

                        // if input empty, do nothing
                        if ($(input).val() == '') { return; }

                        // clone and disable input
                        var copy = $(input).clone();

                        $(copy).attr('disabled', 'disabled');
                        $(copy).removeClass('primary-collection-input');

                        // select target container
                        var display = $(event.currentTarget).closest('.form-group').find('.collection-input-table-display');

                        // move element to table display
                        var row = '<div class="collection-input-row">'
                            + '<div class="col-sm-3 col-xs-12"></div>' + '<div class="col-sm-7 col-xs-10 spacing-inner-medium">' + copy[0].outerHTML + '</div><div style="padding-bottom: 0;" class="col-sm-2 col-xs-2 contour-inner-xsmall"><i class="fa fa-2x fa-times-circle clickable collection-input-del-btn"></i></div></div>';

                        $(display).append(row);
                        $(display).find(':last-child').find('.collection-input').val($(input).val());

                        // attach event handler to 'remove' icon
                        $(display).children(':last-child').find('.collection-input-del-btn').off('click').on('click', PAPALOCAL.form.input.textCollection.remove);

                        // clear input element
                        $(input).val('');
                        $(input).focus();

                    },
                    remove: function(event) {
                        $(event.currentTarget).closest('.collection-input-row').remove();
                    }
                }
            },
            stripeToken: null,
            convertToJson: function(formData) {

                if (typeof formData !== FormData) {
                    throw new TypeError('Argument 1 to convertToJson must be an type of FormData.');
                }

            }
        },
        // displays a Bootstrap flash alert in the site header tray, that auto-closes after @param closeDelay
        alert: function showAlert(message, type, closeDelay) {

            var con = $("#notifications");
            if (con.length > 0) {
                // delete any existing alerts
                con.empty();
            }

            // alerts-container does not exist, add it
            $("body").append( $('<div id=\"notifications\">') );

            // default to alert-info; other options include success, warning, danger
            type = type || "info";

            // create the alert div
            var alert = $('<div id="pop-alert" class="alert alert-' + type + ' fade in col-sm-12">')
                .append($('<button type="button" class="close" data-dismiss="alert">&times;</button>'))
                .append('<p>' + message + '</p>');

            // if closeDelay was passed - set a timeout to close the alert
            if (closeDelay) {
                if (closeDelay < 5000) {
                    window.setTimeout(function () {
                        alert.alert("close")
                    }, 5000);
                } else {
                    window.setTimeout(function () {
                        alert.alert("close")
                    }, closeDelay);
                }

            } else {
                window.setTimeout(function () {
                    alert.alert("close")
                }, 5000);
            }

            // add the alert div to top of alerts-container, use append() to add to bottom
            $(con).append(alert);
        },
        // send a request to the backend to record something in the server logs
        log: function (level, message) {
            try {
                var levels = ['error', 'debug', 'info'];

                //check level is valid
                var levelValid = false;
                for (var i = 0; i < levels.length; i++) {
                    if (level.trim() === levels[i].trim()) {
                        levelValid = true;
                    }
                }

                if (levelValid) {
                    // Whether this request passes or fails cannot be reported to admin.
                    $.ajax({
                        headers: {
                            Accept: "application/json; charset=utf-8"
                        },
                        type: "POST",
                        contentType: "application/json",
                        url: PAPALOCAL.baseUrl + "system/log",
                        data: JSON.stringify({'level': level, 'message': message}),
                        dataType: "json",
                        success: function(data) {
                            // do nothing - message was logged to the server
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // don't log to the server again - it's an infinite loop.
                            console.error(errorThrown);
                        }
                    });
                }
            } catch(error) {
                console.error(error);
            }

        },
        // a list of commonly used messages to the user
        messages: {
            actionFailed: 'Unfortunately, it seems this portion of our site is down.'
        },
        notifications: {
            handleTrayItemClick: function(event) {

                console.log($(event.currentTarget).hasClass('is-read'));
                if (false == $(event.currentTarget).hasClass('is-read')) {

                    // compile request data
                    let requestData = {
                        notificationGuid : event.currentTarget.dataset.notificationGuid,
                        _csrf_token: event.currentTarget.dataset.csrf
                    };

                    // mark notification as read
                    $.ajax({
                        headers: {
                            Accept: "application/json; charset=utf-8"
                        },
                        type: 'POST',
                        contentType: 'application/json',
                        url: PAPALOCAL.baseUrl + '/notification/read',
                        data: JSON.stringify(requestData),
                        dataType: "json",
                        error: function (jqXHR, textStatus, errorThrown) {
                            PAPALOCAL.log('error', 'Mark notification read failed as ' + JSON.stringify(requestData));
                        }
                    });
                }

                // submit feed form
                $(event.currentTarget).parent().submit();
            }
        }
    };

    return {PAPALOCAL};
});