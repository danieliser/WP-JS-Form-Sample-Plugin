/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    var colorpicker = {
        init: function () {
            $('.wpjsfsp-color-picker').filter(':not(.wpjsfsp-color-picker-initialized)')
                .addClass('wpjsfsp-color-picker-initialized')
                .wpColorPicker({
                    change: function (event, ui) {
                        $(event.target).trigger('colorchange', ui);
                    },
                    clear: function (event) {
                        $(event.target).prev().trigger('colorchange').wpColorPicker('close');
                    }
                });
        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.colorpicker = colorpicker;

    $(document)
        .on('click', '.iris-palette', function () {
            $(this).parents('.wp-picker-active').find('input.wpjsfsp-color-picker').trigger('change');
        })
        .on('colorchange', function (event, ui) {
            var $input   = $(event.target),
                $opacity = $input.parents('tr').next('tr.background-opacity'),
                color    = '';

            if (ui !== undefined && ui.color !== undefined) {
                color = ui.color.toString();
            }

            if ($input.hasClass('background-color')) {
                if (typeof color === 'string' && color.length) {
                    $opacity.show();
                } else {
                    $opacity.hide();
                }
            }

            $input.val(color);
        })
        .on('wpjsfsp_init', colorpicker.init);
}(jQuery));