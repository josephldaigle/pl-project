/*
 *   This module integrates the Google Places API, allowing `<input>` elements to utilize Google's address
 *   data when user's are filling out forms.
 */

define(['jquery', '/js/lib/modules/app.js'], function($, PAPALOCAL) {
    if (! google.maps.places) {
        PAPALOCAL.log('error', 'Google Places API did not load as expected.');
        throw new Error('Google Places API initialized.');
    }

    let addressManager = {
        addressLookups: [],
        location: '',
        lastChanged: '',
        locate: function() {
            try {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        let geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        let circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy
                        });

                        addressManager.location = circle.getBounds();

                        $.each(addressManager.addressLookups, function() {
                            if (typeof this.autoComplete !== 'undefined') {
                                this.autoComplete.setBounds(addressManager.location);
                            }
                        });

                    });
                } else {
                    console.error('User chose not to disclose location.');
                }


            } catch (error) {
                console.error('An error occurred while locating the user.');
            }
        },
        bindInputsToApi: function() {

            $.each(this.addressLookups, function () {

                // fetch the address input type (may be a text or select type)
                if (this.type === 'select') {
                    var input = $('form[name="' + this.name + '"]').find(this.type + '[name="selectAddress"]').parent().find('input[type="text"]').get(0);
                } else if (this.type === 'input') {
                    var input = $('form[name="' + this.name + '"]').find(this.type + '[name="selectAddress"]').get(0);
                } else {
                    throw Error('Unable to find an address field (select or input element) that corresponds to ');
                }

                // on pages where input is not available, it will be undefined
                if (input !== undefined) {
                    try {
                        this.autoComplete = new google.maps.places.Autocomplete(
                            input,
                            this.optionsList
                        );

                        //register address conversion listener to place_changed event
                        this.autoComplete.addListener('place_changed',
                            addressManager.addressChanged);

                        if (addressManager.location !== '') {
                            this.autoComplete.setBounds(addressManager.location);
                        }

                    } catch (error) {
                        console.warn(error);
                    }
                }

            });
        },
        /**
         * Update the address fields value on each key up
         */
        attachKeyUpHandlerToInputs: function() {
            // attach event handlers to address fields to locate user
            $('[name="selectAddress"]').each(function() {
                    switch ($(this).prop("tagName")) {
                        case 'INPUT':
                            // address is text input
                            // request user's location service on first key stroke by user
                            $(this).one('keyup', function(){
                                addressManager.locate();

                                if (addressManager.lastChanged == '') {
                                    addressManager.lastChanged = this;
                                }
                            });

                            break;
                        case 'SELECT':  // TODO: Is select used for address?
                            $(this).one('rendered.bs.select', function() {
                                // address is <select> input element
                                // hook after select element is rendered
                                // request user's location service on first button click
                                $(this).parent().find('button').one('click', function() {
                                    addressManager.locate();
                                });

                                // disable liveSearch in bs select for lookups with existing addresses
                                $(this).parent().find('input').on('keyup', function(e) {
                                    e.preventDefault();
                                });

                                // set the last changed element if user makes selection
                                $(this).parent().find('input').on('change', function() {
                                    addressManager.lastChanged = this;
                                });

                                // allow user to select existing address
                                $(this).on('changed.bs.select', function(e) {
                                    // set last changed if user selects an address, instead of typing it
                                    addressManager.lastChanged = this;
                                });

                            });
                            break;
                        default:
                            break;
                    }
            });
        },
        configureAddressLookups: function() {
            /**
             * --------------------------------
             * User Registration
             * --------------------------------
             */
            var userRegistration = {
                name: 'userRegistration',
                type: 'input',
                optionsList: {
                    componentRestrictions: {country: ['us', 'ca']},
                    types: ['address']
                }
            };
            addressManager.addressLookups.push(userRegistration);

            /**
             * --------------------------------
             * Billing & Payments
             * --------------------------------
             */

            // create the addCreditCard configuration
            var addCreditCard = {
                    name: 'addCreditCard',
                    type: 'input',
                    optionsList: {
                        componentRestrictions: {country: ['us', 'ca']},
                        types: ['address']
                    }
                };
            addressManager.addressLookups.push(addCreditCard);

            var addBankAccount = {
                    name: 'addBankAccount',
                    type: 'input',
                    optionsList: {
                        componentRestrictions: {country: ['us', 'ca']},
                        types: ['address']
                    }
                };
            addressManager.addressLookups.push(addBankAccount);

            // var withdrawFunds = {
            //     name: 'withdrawFunds',
            //     type: 'input',
            //     optionsList: {
            //         componentRestrictions: {country: ['us', 'ca']},
            //         types: ['address']
            //     }
            // };
            // addressManager.addressLookups.push(withdrawFunds);

            /**
             * --------------------------------
             * User Profile
             * (user personal address)
             * --------------------------------
             */
            var personProfileAddress = {
                name: 'accountProfileAddress',
                type: 'input',
                optionsList: {
                    componentRestrictions: {country: ['us', 'ca']},
                    types: ['address']
                }
            };
            addressManager.addressLookups.push(personProfileAddress);

            var companyProfileAddress = {
                name: 'companyProfileAddress',
                type: 'input',
                optionsList: {
                    componentRestrictions: {country: ['us', 'ca']},
                    types: ['address']
                }
            };
            addressManager.addressLookups.push(companyProfileAddress);

            var addCompany = {
                name: 'addCompany',
                type: 'input',
                optionsList: {
                    componentRestrictions: {country: ['us', 'ca']},
                    types: ['address']
                }
            };
            addressManager.addressLookups.push(addCompany);

            /**
             * --------------------------------
             * Referrals & Agreements
             * --------------------------------
             */
            var addReferral = {
                name: 'addReferral',
                type: 'input',
                optionsList: {
                    componentRestrictions: {country: ['us', 'ca']},
                    types: ['address']
                }
            };
            addressManager.addressLookups.push(addReferral);
        },
        addressChanged: function() {
            // get the place details from the auto-complete object.
            var place = this.getPlace();

            // update select picker when a google address is chosen
            var lastChanged = addressManager.lastChanged;

            if (lastChanged !== '' && $(lastChanged).parent().hasClass('bs-searchbox')) {
                PAPALOCAL.log('debug', 'The UI is using select elements for address fields.');

                // last changed is a select picker element
                // update the select picker with google searched address

                var selectPicker = $(lastChanged).closest('.bootstrap-select').find('select');

                // append google selected address to bs select element
                selectPicker.append('<option value = "-1">' + place.formatted_address + '</option>');

                // remove placeholder if it exists
                selectPicker.find('[value=0]').remove();

                // refresh and render the select element
                selectPicker.selectpicker('refresh');
                selectPicker.selectpicker('render');

                selectPicker.selectpicker('val', '-1');

                // toggle the select if it is open
                if ($(lastChanged).closest('.bootstrap-select').hasClass('open')) {
                    selectPicker.selectpicker('toggle');
                }

            }

            // set up expected values
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',                             // road
                locality: 'long_name',                          // city
                administrative_area_level_1: 'long_name',       // state
                country: 'long_name',
                postal_code: 'short_name'
            };

            // Get each component of the address from the place details
            // and fill the corresponding field on the form
            // fetch the form for the changed element
            try {
                var form = $(lastChanged).closest('form').find('div.addressPlaceholder');

                for (var i = 0; i < place.address_components.length; i++) {

                    // get the field type (this is the name of the field)
                    var addressType = place.address_components[i].types[0];

                    if (componentForm[addressType]) {
                        // get the place detail
                        var val = place.address_components[i][componentForm[addressType]];

                        // set the google-searched address in the placeholder on the form
                        var addressPlaceholder = $(form).find('input[name="' + addressType + '"]');
                        addressPlaceholder.prop('disabled', false);
                        addressPlaceholder.val(val);
                    }
                }
            } catch (error) {
                PAPALOCAL.log('debug', 'There was an error while tyring to set the address values on the form.');
                console.log('There was an error that will prevent you from updating your address at this time.');
            }
        }
    };

    return {addressManager};
});