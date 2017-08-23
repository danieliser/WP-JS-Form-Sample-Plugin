/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    var models = {
        field: function (args) {
            return $.extend(true, {}, {
                type: 'text',
                id: '',
                id_prefix: '',
                name: '',
                label: null,
                placeholder: '',
                desc: null,
                dynamic_desc: null,
                size: 'regular',
                classes: [],
                dependencies: "",
                value: null,
                select2: false,
                multiple: false,
                as_array: false,
                options: [],
                object_type: null,
                object_key: null,
                std: null,
                min: 0,
                max: 50,
                step: 1,
                unit: 'px',
                units: {},
                required: false,
                desc_position: 'bottom',
                meta: {}
            }, args);
        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.models = models;
}(jQuery));