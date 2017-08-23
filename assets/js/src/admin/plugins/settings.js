/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";
    window.wpjsfsp_settings_editor = window.wpjsfsp_settings_editor || {
        form_args: {},
        current_values: {}
    };

    $(document)
        .ready(function () {
            var $container = $('#wpjsfsp-settings-container'),
                args       = wpjsfsp_settings_editor.form_args || {},
                values     = wpjsfsp_settings_editor.current_values || {};

            if ($container.length) {
                WPJSFSP.forms.render(args, values, $container);
            }
        });
}(jQuery));