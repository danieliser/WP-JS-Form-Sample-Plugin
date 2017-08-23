/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    var $document = $(document);

    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.I10n = wpjsfsp_admin_vars.I10n;

    $.fn.wp_editor = WPJSFSP.wp_editor || {};

    // Kick things off & initialize various modules.
    $document
        .ready(function () {
            $document.trigger('wpjsfsp_init');

            // TODO Can't figure out why this is needed, but it looks stupid otherwise when the first condition field defaults to something other than the placeholder.
            $('#wpjsfsp-first-condition')
                .val(null)
                .trigger('change');
        })
        .on('keydown', '#message-headline', function (event) {
            var keyCode = event.keyCode || event.which;
            if (9 === keyCode) {
                event.preventDefault();
                $('#title').focus();
            }
        })
        .on('keydown', '#title, #message-headline', function (event) {
            var keyCode = event.keyCode || event.which,
                target;
            if (!event.shiftKey && 9 === keyCode) {
                event.preventDefault();
                target = $(this).attr('id') === 'title' ? '#message-headline' : '#insert-media-button';
                $(target).focus();
            }
        })
        .on('keydown', '#message-headline, #insert-media-button', function (event) {
            var keyCode = event.keyCode || event.which,
                target;
            if (event.shiftKey && 9 === keyCode) {
                event.preventDefault();
                target = $(this).attr('id') === 'message-headline' ? '#title' : '#message-headline';
                $(target).focus();
            }
        });


}(jQuery));