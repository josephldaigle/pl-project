define(['jquery', 'bootstrapMoment', '/js/lib/modules/app.js', '/js/lib/ext/star-rating.js'], function($, moment, PAPALOCAL, starRating) {

    PAPALOCAL = PAPALOCAL.PAPALOCAL;

    /**
     * Model the feed filter.
     */
    let filter = {
        form: '',   // var to hold form's settings for replacement if the user does not submit
        /**
         * Opens the feed filter form.
         */
        open: function(event){

            // clone the form for replacement of initial settings if user does not apply changes
            filter.form = $('#feed-filter-form').clone(true, true);

            // un-hide feed-filter/feed-sort ui components
            $('#advance-filter, #sort-filter, #apply-filter').removeClass('hide');

            // show the modal backdrop
            if( !($('.modal-backdrop.in').length > 0) ) {
                $('body').append('<div class="modal-backdrop in"></div>');
                $("#feed-filter-container").css({"position": "relative", "z-index": "1041"});

                // attach close function to backdrop
                $('.modal-backdrop.in').on('click', filter.close);
            }
        },
        /**
         * Closes the feed filter form.
         */
        close: function() {
            // replace the form
            $('#feed-filter-form').replaceWith(filter.form);

            // hide the filter
            $('#advance-filter, #sort-filter, #apply-filter').addClass('hide');

            // remove the modal backdrop
            $('body').find('.modal-backdrop.in').remove();
            $('#feed-filter-container').css({'position': 'initial', 'z-index': '1040'});
        },
        /**
         * Event handler for feed filter types icon.
         *
         * @param event
         */
        selectType: function(event) {
            $('#notification-input').remove();

            // get selected value
            let selectedValue = $(event.currentTarget).attr('value');

            // toggle icon
            if (selectedValue === 'all') {
                // toggle all icons to 'active' state
                $('#feed-type-filter').find('span[id$="-icon"]').each(function() {
                    $(this).find('i:first-child').removeClass('inactive-icon');
                });

                // set all inputs to 'checked'
                $('#feed-type-filter').find('input:not(:checked)').each(function () {
                    $(this).prop('checked', true);
                });

            } else {
                // a single-type filter was selected
                // toggle state for selected icon
                $('#feed-type-filter').find('span#' + selectedValue + '-icon>i:first-child').toggleClass('inactive-icon');

                // toggle 'inactive' state for 'all' icon
                $('#feed-type-filter').find('span#all-icon>i:first-child').addClass('inactive-icon');
                // un-check 'all' input
                $('#feed-type-filter').find('input#all-input').prop('checked', false);

                if ($('#feed-type-filter').find('input:checked').length === 3) {
                    // select tshe all icon if all inidvidual inputs are checked
                    $('#feed-type-filter').find('input#all-input').click();
                }
            }

        },
        /**
         * Date handler for feed filter. This function updates the selected date values when the user chooses a predefined filter.
         *
         * @param selector
         */
        selectDate: function (event){

            var selector = '#' + event.currentTarget.value.replace(/\s/g, '-').toLowerCase();

            $(selector).toggleClass('primary-color primary-background');

            if ( !($('#today, #yesterday, #last-7-days, #last-30-days, #last-month, #this-month').hasClass('primary-color primary-background')) ){
                $('#date-time-picker-start, #date-time-picker-end').val('');
            }

            if ( $(selector).hasClass('primary-color primary-background') ) {

                var filterDate = selector.slice(1);
                switch (filterDate) {
                    case 'today':
                        $('#date-time-picker-start, #date-time-picker-end').val( moment().format('MM/DD/YYYY') );
                        $('#all-time, #yesterday, #last-7-days, #last-30-days, #last-month, #this-month').removeClass('primary-color primary-background');
                        break;

                    case 'yesterday':
                        $('#date-time-picker-start, #date-time-picker-end').val( moment().subtract(1, 'day').format('MM/DD/YYYY') );
                        $('#today, #all-time, #last-7-days, #last-30-days, #last-month, #this-month').removeClass('primary-color primary-background');
                        break;

                    case 'last-7-days':
                        $('#date-time-picker-start').val( moment().subtract(6, 'day').format('MM/DD/YYYY') );
                        $('#date-time-picker-end').val( moment().format('MM/DD/YYYY') );
                        $('#today, #yesterday, #all-time, #last-30-days, #last-month, #this-month').removeClass('primary-color primary-background');
                        break;

                    case 'last-30-days':
                        $('#date-time-picker-start').val( moment().subtract(29, 'day').format('MM/DD/YYYY') );
                        $('#date-time-picker-end').val( moment().format('MM/DD/YYYY') );
                        $('#today, #yesterday, #last-7-days, #all-time, #last-month, #this-month').removeClass('primary-color primary-background');
                        break;

                    case 'last-month':
                        $('#date-time-picker-start').val( moment().subtract(1, 'months').startOf('month').format('MM/DD/YYYY') );
                        $('#date-time-picker-end').val( moment().subtract(1, 'months').endOf('month').format('MM/DD/YYYY') );
                        $('#today, #yesterday, #last-7-days, #last-30-days, #all-time, #this-month').removeClass('primary-color primary-background');
                        break;

                    case 'this-month':
                        $('#date-time-picker-start').val( moment().startOf('month').format('MM/DD/YYYY') );
                        $('#date-time-picker-end').val( moment().format('MM/DD/YYYY') );
                        $('#today, #yesterday, #last-7-days, #last-30-days, #last-month, #all-time').removeClass('primary-color primary-background');
                        break;

                    case 'all-time':
                        $('#date-time-picker-start').val( '01/01/2015' );
                        $('#date-time-picker-end').val( moment().format('MM/DD/YYYY') );
                        $('#today, #yesterday, #last-7-days, #last-30-days, #last-month, #this-month').removeClass('primary-color primary-background');
                        break;

                    default:
                        // set selectedDate = 'All Time';
                        $('#today, #yesterday, #last-7-days, #last-30-days, #last-month, #this-month').removeClass('primary-color primary-background');
                }
            }
        }

    };

    /**
     * Scrolls selected feed item into view.
     *
     * @param type the feed type
     * @param id the feed item id
     */
    let scrollToItem = function(type, id) {
        // scroll to the item
        let container = $('#feed-item'),
            scrollTo = $('[name="' + type + id + '"]');

        container.scrollTop(
            scrollTo.offset().top - container.offset().top + container.scrollTop()
        );
    };
    /**
     * Handles event when user scrolls to bottom of feed tray.
     */
    let scrollToEnd = function(event) {
        if ($(event.currentTarget)[0].scrollHeight - $(event.currentTarget).scrollTop() === $(event.currentTarget)[0].clientHeight) {

            // load feed item set
            let data = PAPALOCAL.form.serialize($('#feed-filter-container').find('#feed-filter-form')[0]);

            data.fetchCount = 15;
            data.beginWith = $('#feed-item').find('div[data-feed-id]').length + 1;


            try {
                $.ajax({
                    headers: {
                        Accept: "application/json; charset=utf-8"
                    },
                    type: 'POST',
                    contentType: 'application/json',
                    url: PAPALOCAL.baseUrl + 'feed/items/filter',
                    data: JSON.stringify(data),
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        if (data.item_detail) {
                            $('#feed-item').html(data.item_detail);
                        } else {
                            throw new Error('The response for feed/items/filter did not return item_detail.');
                        }

                        attachCardClickHandler();

                        if ($(window).width() <= 767){

                            $('#feed-back-button').off('click').on('click',function(e){
                                $('#feed-item-container').toggleClass('hide');
                                $('#feed-detail-container').toggleClass('hide');
                                $('#feed-back-button').toggleClass('hide');
                            });

                            $('div.feed-card-container>div.panel-heading, div.feed-card-container>div.panel-body').on('click',function(e){
                                $('#feed-item-container').toggleClass('hide');
                                $('#feed-detail-container').toggleClass('hide');
                                $('#feed-back-button').toggleClass('hide');

                            });
                        }
                    },
                    error: function(jqHXR, textStatus, errorThrown) {
                        throw new Error('Error response received from /feed/items/filter.');
                    }
                });
            } catch (error) {
                PAPALOCAL.log('error', error.toString());
                console.log('An error occurred while trying to load more feed items.', errorThrown);
            }

        }
    };
    /**
     * Selects a feed item from column 1, and displays it's detail in column 2.
     *
     * @param event
     */
    let selectItem = function (event) {
        var selectedItem = $(event.currentTarget).children().filter('[data-feed-type]');

        if (selectedItem == undefined) {
            PAPALOCAL.log('error', 'An attempt to locate a feed item failed.');
            return;
        }

        // send call to load item into feed display column
        $.ajax({
            headers: {
                Accept: "application/json; charset=utf-8"
            },
            type: 'POST',
            contentType: 'application/json',
            url: PAPALOCAL.baseUrl + 'feed/item',
            data: JSON.stringify({
                type: $(selectedItem).data('feed-type'),
                guid: $(selectedItem).data('feed-id'),
                _csrf_token: $('#feed-item').data('csrf-token')
            }),
            dataType: 'json',
            success: function (data, textStatus, jqXHR) {
                // show items feed detail in col 2 of the feed
                if ($('#feed-detail').children().length > 0) {
                    $('#feed-detail').children(0).replaceWith(data.item_detail);
                } else {
                    $('#feed-detail').append(data.item_detail);
                }

                // style the previously selected item (no longer the current selection
                let prevSelectedItem = $('div[data-feed-type]').filter('.selected-feed-item');
                if (prevSelectedItem.length !== 0) {
                    // $(prevSelectedItem).find('i.inactive-icon').toggleClass('inactive-icon');
                    $(prevSelectedItem).removeClass('selected-feed-item');
                }


                // style card list item
                $('div[data-feed-type]').filter('.selected-feed-item').removeClass('selected-feed-item');
                $(selectedItem).addClass('selected-feed-item');

                // Initialize star rating with plugin options
                $("#referral-rate").rating({
                    min:0,
                    max:5,
                    step:1,
                    size:'xs',
                    disabled: true,
                    animate: false,
                    showClear: false,
                    starCaptions: function(val) {
                        if (val < 3) {
                            return 'Dispute';
                        } else {
                            return val + ' stars';
                        }
                    },
                    starCaptionClasses: function(val) {
                        if (val < 3) {
                            return 'label label-danger';
                        }
                        else {
                            return 'label label-success';
                        }
                    }
                });

                // attach event handlers to document
                PAPALOCAL.attachEventHandlers();
            },
            error: function (jqHXR, textStatus, errorThrown) {
                console.error(errorThrown);
                PAPALOCAL.alert(PAPALOCAL.messages.actionFailed, 'danger');
            }
        });

    };

    /**
     * Attach the click handler function to each card in the feed tray.
     */
    let attachCardClickHandler = function() {
        // attach feed card click handler
        $('.feed-card-container:not(#feed-filter-container)').each(function(index, item) {
            $(item).on('click', selectItem);
        });
    };

    return {filter, scrollToItem, scrollToEnd, selectItem, attachCardClickHandler};
});
