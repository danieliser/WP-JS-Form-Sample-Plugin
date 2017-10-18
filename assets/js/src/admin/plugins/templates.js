/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";
    var templates = {
        render: function (template, data) {
            var _template = wp.template(template);

            data = data || {};

            if (data.classes !== undefined && Array.isArray(data.classes)) {
                data.classes = data.classes.join(' ');
            }

            // Prepare the meta data for templates.
            data = WPJSFSP.templates.prepareMeta(data);

            return _template(data);
        },
        renderInline: function (content, data) {
            var options  = {
                    evaluate: /<#([\s\S]+?)#>/g,
                    interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                    escape: /\{\{([^\}]+?)\}\}(?!\})/g,
                    variable: 'data'
                },
                template = _.template(content, null, options);

            return template(data);
        },
        shortcode: function (args) {
            var data     = $.extend(true, {}, {
                    tag: '',
                    meta: {},
                    has_content: false,
                    content: ''
                }, args),
                template = data.has_content ? 'wpjsfsp-shortcode-w-content' : 'wpjsfsp-shortcode';

            return WPJSFSP.templates.render(template, data);
        },
        modal: function (args) {
            var data = $.extend(true, {}, {
                id: '',
                title: '',
                description: '',
                classes: '',
                save_button: window.WPJSFSP.I10n.save,
                cancel_button: window.WPJSFSP.I10n.cancel,
                content: ''
            }, args);

            return WPJSFSP.templates.render('wpjsfsp-modal', data);
        },
        tabs: function (data) {
            data = $.extend(true, {}, {
                id: '',
                vertical: false,
                form: false,
                classes: [],
                tabs: {},
                meta: {}
            }, data);

            if (typeof data.classes === 'string') {
                data.classes = [data.classes];
            }

            if (data.form) {
                data.classes.push('wpjsfsp-tabbed-form');
            }

            data.meta['data-tab-count'] = Object.keys(data.tabs).length;

            data.classes.push(data.vertical ? 'vertical-tabs' : 'horizontal-tabs');

            data.classes = data.classes.join('  ');

            return WPJSFSP.templates.render('wpjsfsp-tabs', data);
        },
        section: function (args) {
            var data = $.extend(true, {}, {
                classes: [],
                fields: []
            }, args);


            return WPJSFSP.templates.render('wpjsfsp-field-section', data);
        },
        fieldArgs: function (args) {
            var options = [],
                data    = $.extend(true, {}, WPJSFSP.models.field(args));

            if (!data.value && args.std !== undefined) {
                data.value = args.std;
            }

            if ('string' === typeof data.classes) {
                data.classes = data.classes.split(' ');
            }

            if (args.class !== undefined) {
                data.classes.push(args.class);
            }

            if (args.dependencies !== undefined && typeof args.dependencies === 'object') {
                data.dependencies = JSON.stringify(args.dependencies);
            }

            if (data.required) {
                data.meta.required = true;
                data.classes.push('wpjsfsp-required');
            }

            if (typeof data.dynamic_desc === 'string' && data.dynamic_desc.length) {
                data.classes.push('wpjsfsp-field-dynamic-desc');
                data.desc = WPJSFSP.templates.renderInline(data.dynamic_desc, data);
            }

            switch (args.type) {
            case 'select':
            case 'objectselect':
            case 'postselect':
            case 'taxonomyselect':
                if (data.options !== undefined) {
                    _.each(data.options, function (label, value) {
                        var selected = false,
                            optgroup,
                            optgroup_options;

                        // Check if the label is an object. If so this is a optgroup and the label is sub options array.
                        // NOTE: The value in the case its an optgroup is the optgroup label.
                        if (typeof label !== 'object') {

                            if (data.value !== null) {
                                if (data.multiple && ((typeof data.value === 'object' && Object.keys(data.value).length && data.value[value] !== undefined) || (Array.isArray(data.value) && data.value.indexOf(value) !== -1))) {
                                    selected = 'selected';
                                } else if (!data.multiple && data.value == value) {
                                    selected = 'selected';
                                }
                            }

                            options.push(
                                WPJSFSP.templates.prepareMeta({
                                    label: label,
                                    value: value,
                                    meta: {
                                        selected: selected
                                    }
                                })
                            );

                        } else {
                            // Process Option Groups

                            // Swap label & value due to group labels being used as keys.
                            optgroup = value;
                            optgroup_options = [];

                            _.each(label, function (label, value) {
                                var selected = false;

                                if (data.value !== null) {
                                    if (data.multiple && ((typeof data.value === 'object' && Object.keys(data.value).length && data.value[value] !== undefined) || (Array.isArray(data.value) && data.value.indexOf(value) !== -1))) {
                                        selected = 'selected';
                                    } else if (!data.multiple && data.value == value) {
                                        selected = 'selected';
                                    }
                                }
                                optgroup_options.push(
                                    WPJSFSP.templates.prepareMeta({
                                        label: label,
                                        value: value,
                                        meta: {
                                            selected: selected
                                        }
                                    })
                                );

                            });

                            options.push({
                                label: optgroup,
                                options: optgroup_options
                            });

                        }

                    });

                    data.options = options;

                }

                if (data.multiple) {

                    data.meta.multiple = true;

                    if (data.as_array) {
                        data.name += '[]';
                    }

                    if (!data.value || !data.value.length) {
                        data.value = [];
                    }

                    if (typeof data.value === 'string') {
                        data.value = [data.value];
                    }

                }

                if (args.type !== 'select') {
                    data.select2 = true;
                    data.classes.push('wpjsfsp-field-objectselect');
                    data.classes.push(args.type === 'postselect' ? 'wpjsfsp-field-postselect' : 'wpjsfsp-field-taxonomyselect');
                    data.meta['data-objecttype'] = args.type === 'postselect' ? 'post_type' : 'taxonomy';
                    data.meta['data-objectkey'] = args.type === 'postselect' ? args.post_type : args.taxonomy;
                    data.meta['data-current'] = typeof data.value === 'object' || Array.isArray(data.value) ? JSON.stringify(data.value) : data.value;
                }

                if (data.select2) {
                    data.classes.push('wpjsfsp-field-select2');

                    if (data.placeholder) {
                        data.meta['data-placeholder'] = data.placeholder;
                    }
                }

                break;
            case 'radio':
                if (data.options !== undefined) {
                    _.each(data.options, function (label, value) {

                        options.push(
                            WPJSFSP.templates.prepareMeta({
                                label: label,
                                value: value,
                                meta: {
                                    checked: data.value === value
                                }
                            })
                        );

                    });

                    data.options = options;
                }
                break;
            case 'multicheck':
                if (data.options !== undefined) {

                    if (!data.value) {
                        data.value = [];
                    }

                    if (data.as_array) {
                        data.name += '[]';
                    }

                    _.each(data.options, function (label, value) {

                        options.push(
                            WPJSFSP.templates.prepareMeta({
                                label: label,
                                value: value,
                                meta: {
                                    checked: (typeof data.value === 'object' && data.value[value] !== undefined) || (typeof data.value === 'array' && data.value.indexOf(value) >= 0)
                                }
                            })
                        );

                    });

                    data.options = options;
                }
                break;
            case 'checkbox':
                if (parseInt(data.value, 10) === 1) {
                    data.meta.checked = true;
                }
                break;
            case 'rangeslider':
                // data.meta.readonly = true;
                data.meta.step = data.step;
                data.meta.min = data.min;
                data.meta.max = data.max;
                break;
            case 'textarea':
                data.meta.cols = data.cols;
                data.meta.rows = data.rows;
                break;
            case 'measure':
                if (typeof data.value === 'string' && data.value !== '') {
                    data.number = parseInt(data.value);
                    data.unitValue = data.value.replace(data.number, "");
                    data.value = data.number;
                } else {
                    data.unitValue = null;
                }

                if (data.units !== undefined) {
                    _.each(data.units, function (label, value) {
                        var selected = false;

                        if (data.unitValue == value) {
                            selected = 'selected';
                        }

                        options.push(
                            WPJSFSP.templates.prepareMeta({
                                label: label,
                                value: value,
                                meta: {
                                    selected: selected
                                }
                            })
                        );

                    });

                    data.units = options;
                }
                break;
            case 'license_key':

                data.value = $.extend({
                    key: '',
                    license: {},
                    messages: [],
                    status: 'empty',
                    expires: false,
                    classes: false
                }, data.value);

                data.classes.push('wpjsfsp-license-' + data.value.status + '-notice');

                if (data.value.classes) {
                    data.classes.push(data.value.classes);
                }
                break;
            }

            return data;
        },
        field: function (args) {
            var fieldTemplate,
                data = WPJSFSP.templates.fieldArgs(args);

            fieldTemplate = 'wpjsfsp-field-' + data.type;

            if (!$('#tmpl-' + fieldTemplate).length) {
                if (data.type === 'objectselfect' || data.type === 'postselect' || data.type === 'taxonomyselect') {
                    fieldTemplate = 'wpjsfsp-field-select';
                }
                if (!$('#tmpl-' + fieldTemplate).length) {
                    return '';
                }
            }

            data.field = WPJSFSP.templates.render(fieldTemplate, data);

            return WPJSFSP.templates.render('wpjsfsp-field-wrapper', data);
        },
        prepareMeta: function (data) {
            // Convert meta JSON to attribute string.
            var _meta = [],
                key;

            for (key in data.meta) {
                if (data.meta.hasOwnProperty(key)) {
                    // Boolean attributes can only require attribute key, not value.
                    if ('boolean' === typeof data.meta[key]) {
                        // Only set truthy boolean attributes.
                        if (data.meta[key]) {
                            _meta.push(_.escape(key));
                        }
                    } else {
                        _meta.push(_.escape(key) + '="' + _.escape(data.meta[key]) + '"');
                    }
                }
            }

            data.meta = _meta.join(' ');
            return data;
        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.templates = templates;
}(jQuery));