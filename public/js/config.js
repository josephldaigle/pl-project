/**
 * Created by Joe Daigle on 1/4/19.
 */

/** This is the main application file, which manages and loads site-wide dependencies. **/

requirejs.config({
        baseUrl: "/js/lib/modules/",
        paths: {
            jquery: "//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min",
            jqueryMask: "//cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min",
            bootstrap: "//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min",
            bootstrapSelect: "//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min",
            bootstrapMoment: "//cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min"
        },
        shim: {
            'bootstrap': {
                deps: ['jquery'],
                exports: 'bootstrap'
            }
        }
});