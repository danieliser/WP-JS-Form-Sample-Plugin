/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
var wpActiveEditor = true;
(function ($) {
    "use strict";

    var current_link_field;

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};

    $(document)
        .on('click', '.wpjsfsp-field-link button', function (event) {
            var $input = $(this).next().select(),
                id     = $input.attr('id');

            current_link_field = $input;

            wpLink.open(id, $input.val(), ""); //open the link popup

            WPJSFSP.selectors('#wp-link-wrap').removeClass('has-text-field');
            WPJSFSP.selectors('#wp-link-target').hide();

            event.preventDefault();
            event.stopPropagation();
            return false;
        })
        .on('click', '#wp-link-submit, #wp-link-cancel button, #wp-link-close', function (event) {
            var linkAtts = wpLink.getAttrs();

            // If not for our fields then ignore it.
            if (current_link_field === undefined || !current_link_field) {
                return;
            }

            // If not the close buttons then its the save button.
            if (event.target.id === 'wp-link-submit') {
                current_link_field.val(linkAtts.href);
            }

            wpLink.textarea = current_link_field;
            wpLink.close();

            // Clear the current_link_field
            current_link_field = false;

            //trap any other events
            event.preventDefault ? event.preventDefault() : event.returnValue = false;
            event.stopPropagation();
            return false;
        });
}(jQuery));