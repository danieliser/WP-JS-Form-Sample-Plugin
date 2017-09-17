(function ($) {
    "use strict";


    var forms = {
        init: function () {
            forms.checkDependencies();
        },
        /**
         * dependencies should look like this:
         *
         * {
                 *   field_name_1: value, // Select, radio etc.
                 *   field_name_2: true // Checkbox
                 * }
         *
         * Support for Multiple possible values of one field
         *
         * {
                 *   field_name_1: [ value_1, value_2 ]
                 * }
         *
         */
        checkDependencies: function ($dependent_fields) {
            var _fields = $($dependent_fields);

            // If no fields passed, only do those not already initialized.
            $dependent_fields = _fields.length ? _fields : $("[data-wpjsfsp-dependencies]:not([data-wpjsfsp-processed-dependencies])");

            $dependent_fields.each(function () {
                var $dependent      = $(this),
                    dependentID     = $dependent.data('id'),
                    // The dependency object for this field.
                    dependencies    = $dependent.data("wpjsfsp-processed-dependencies") || {},
                    // Total number of fields this :input is dependent on.
                    requiredCount   = Object.keys(dependencies).length,
                    // Current count of fields this :input matched properly.
                    count           = 0,
                    // An array of fields this :input is dependent on.
                    dependentFields = $dependent.data("wpjsfsp-dependent-fields"),
                    // Early declarations.
                    key;

                // Clean up & pre-process dependencies so we don't need to rebuild each time.
                if (!$dependent.data("wpjsfsp-processed-dependencies")) {
                    dependencies = $dependent.data("wpjsfsp-dependencies");
                    if (typeof dependencies === 'string') {
                        dependencies = JSON.parse(dependencies);
                    }

                    // Convert each key to an array of acceptable values.
                    for (key in dependencies) {
                        if (dependencies.hasOwnProperty(key)) {
                            if (typeof dependencies[key] === "string") {
                                // Leave boolean values alone as they are for checkboxes or checking if an input has any value.

                                if (dependencies[key].indexOf(',') !== -1) {
                                    dependencies[key] = dependencies[key].split(',');
                                } else {
                                    dependencies[key] = [dependencies[key]];
                                }
                            }
                        }
                    }

                    // Update cache & counts.
                    requiredCount = Object.keys(dependencies).length;
                    $dependent.data("wpjsfsp-processed-dependencies", dependencies).attr("data-wpjsfsp-processed-dependencies", dependencies);
                }

                if (!dependentFields) {
                    dependentFields = $.map(dependencies, function (value, index) {
                        var $wrapper = $('.wpjsfsp-field[data-id="' + index + '"]');

                        return $wrapper.length ? $wrapper.eq(0) : null;
                    });

                    $dependent.data("wpjsfsp-dependent-fields", dependentFields);
                }

                $(dependentFields).each(function () {
                    var $wrapper                   = $(this),
                        $field                     = $(this).find(':input:first'),
                        id                         = $wrapper.data("id"),
                        value                      = $field.val(),
                        required                   = dependencies[id] || null,
                        matched,
                        // Used for limiting the fields that get updated when this field is changed.
                        all_this_fields_dependents = $wrapper.data('wpjsfsp-field-dependents');

                    if (!all_this_fields_dependents) {
                        all_this_fields_dependents = [];
                    }

                    if (all_this_fields_dependents.indexOf(dependentID) === -1) {
                        all_this_fields_dependents.push(dependentID);
                        $wrapper.data('wpjsfsp-field-dependents', all_this_fields_dependents);
                    }

                    // If no required values found bail early.
                    if (required === null) {
                        $dependent.hide();
                        // Effectively breaks the .each for this $dependent and hides it.
                        return false;
                    }

                    if ($wrapper.hasClass('wpjsfsp-field-radio')) {
                        value = $wrapper.find(':input:checked').val();
                    }

                    // Check if the value matches required values.
                    if ($wrapper.hasClass('wpjsfsp-field-select') || $wrapper.hasClass('wpjsfsp-field-radio')) {
                        matched = required.indexOf(value) !== -1;
                    } else if ($wrapper.hasClass('wpjsfsp-field-checkbox')) {
                        matched = required === $field.is(':checked');
                    } else {
                        matched = Array.isArray(required) ? required.indexOf(value) !== -1 : required == value;
                    }

                    if (matched) {
                        count++;
                    } else {
                        $dependent.hide();
                        // Effectively breaks the .each for this $dependent and hides it.
                        return false;
                    }

                    if (count === requiredCount) {
                        $dependent.show();
                    }
                });
            });
        },
        form_check: function () {
            $(document).trigger('wpjsfsp_form_check');
        },
        is_field: function (data) {
            if (typeof data !== 'object') {
                return false;
            }

            var field_tests = [
                data.id !== undefined,
                data.label !== undefined,
                data.type !== undefined,
                data.options !== undefined,
                data.desc !== undefined
            ];

            return field_tests.indexOf(true) >= 0;
        },
        parseFields: function (fields, values) {

            values = values || {};

            _.each(fields, function (field, fieldID) {

                fields[fieldID] = WPJSFSP.models.field(field);

                if (typeof fields[fieldID].meta !== 'object') {
                    fields[fieldID].meta = {};
                }

                if (undefined !== values[fieldID]) {
                    fields[fieldID].value = values[fieldID];
                }

                if (fields[fieldID].id === '') {
                    fields[fieldID].id = fieldID;
                }
            });

            return fields;
        },
        renderTab: function () {

        },
        renderSection: function () {

        },
        render: function (args, values, $container) {
            var form,
                sections          = {},
                section           = [],
                form_fields       = {},
                data              = $.extend(true, {
                    id: "",
                    tabs: {},
                    sections: {},
                    fields: {},
                    maintabs: {},
                    subtabs: {}
                }, args),
                maintabs          = $.extend({
                    id: data.id,
                    classes: [],
                    tabs: {},
                    vertical: true,
                    form: true,
                    meta: {
                        'data-min-height': 250
                    }
                }, data.maintabs),
                subtabs           = $.extend({
                    classes: ['link-tabs', 'sub-tabs'],
                    tabs: {}
                }, data.subtabs),
                container_classes = ['wpjsfsp-dynamic-form'];

            values = values || {};

            if (Object.keys(data.tabs).length && Object.keys(data.sections).length) {
                container_classes.push('tabbed-content');

                // Loop Tabs
                _.each(data.fields, function (subTabs, tabID) {

                    // If not a valid tab or no subsections skip it.
                    if (typeof subTabs !== 'object' || !Object.keys(subTabs).length) {
                        return;
                    }

                    // Define this tab.
                    if (undefined === maintabs.tabs[tabID]) {
                        maintabs.tabs[tabID] = {
                            label: data.tabs[tabID],
                            content: ''
                        };
                    }

                    // Define the sub tabs model.
                    subtabs = $.extend(subtabs, {
                        id: data.id + '-' + tabID + '-subtabs',
                        tabs: {}
                    });

                    // Loop Tab Sections
                    _.each(subTabs, function (subTabFields, subTabID) {

                        // If not a valid subtab or no fields skip it.
                        if (typeof subTabFields !== 'object' || !Object.keys(subTabFields).length) {
                            return;
                        }

                        // Move single fields into the main subtab.
                        if (forms.is_field(subTabFields)) {
                            var newSubTabFields = {};
                            newSubTabFields[subTabID] = subTabFields;
                            subTabID = 'main';
                            subTabFields = newSubTabFields;
                        }

                        // Define this subtab model.
                        if (undefined === subtabs.tabs[subTabID]) {
                            subtabs.tabs[subTabID] = {
                                label: data.sections[tabID][subTabID],
                                content: ''
                            };
                        }

                        subTabFields = forms.parseFields(subTabFields, values);

                        // Loop Tab Section Fields
                        _.each(subTabFields, function (field) {
                            // Store the field by id for easy lookup later.
                            form_fields[field.id] = field;

                            // Push rendered fields into the subtab content.
                            subtabs.tabs[subTabID].content += WPJSFSP.templates.field(field);
                        });

                        // Remove any empty tabs.
                        if ("" === subtabs.tabs[subTabID].content) {
                            delete subtabs.tabs[subTabID];
                        }
                    });

                    // If there are subtabs, then render them into the main tabs content, otherwise remove this main tab.
                    if (Object.keys(subtabs.tabs).length) {
                        maintabs.tabs[tabID].content = WPJSFSP.templates.tabs(subtabs);
                    } else {
                        delete maintabs.tabs[tabID];
                    }
                });

                if (Object.keys(maintabs.tabs).length) {
                    form = WPJSFSP.templates.tabs(maintabs);
                }
            }
            else if (Object.keys(data.tabs).length) {
                container_classes.push('tabbed-content');

                // Loop Tabs
                _.each(data.fields, function (tabFields, tabID) {

                    // If not a valid tab or no subsections skip it.
                    if (typeof tabFields !== 'object' || !Object.keys(tabFields).length) {
                        return;
                    }

                    // Define this tab.
                    if (undefined === maintabs.tabs[tabID]) {
                        maintabs.tabs[tabID] = {
                            label: data.tabs[tabID],
                            content: ''
                        };
                    }

                    section = [];

                    tabFields = forms.parseFields(tabFields, values);

                    // Loop Tab Fields
                    _.each(tabFields, function (field) {
                        // Store the field by id for easy lookup later.
                        form_fields[field.id] = field;

                        // Push rendered fields into the subtab content.
                        section.push(WPJSFSP.templates.field(field));
                    });

                    // Push rendered tab into the tab.
                    if (section.length) {
                        // Push rendered sub tabs into the main tabs if not empty.
                        maintabs.tabs[tabID].content = WPJSFSP.templates.section({
                            fields: section
                        });
                    } else {
                        delete (maintabs.tabs[tabID]);
                    }
                });

                if (Object.keys(maintabs.tabs).length) {
                    form = WPJSFSP.templates.tabs(maintabs);
                }
            }
            else if (Object.keys(data.sections).length) {

                // Loop Sections
                _.each(data.fields, function (sectionFields, sectionID) {
                    section = [];

                    section.push(WPJSFSP.templates.field({
                        type: 'heading',
                        desc: data.sections[sectionID] || ''
                    }));

                    sectionFields = forms.parseFields(sectionFields, values);

                    // Loop Tab Section Fields
                    _.each(sectionFields, function (field) {
                        // Store the field by id for easy lookup later.
                        form_fields[field.id] = field;

                        // Push rendered fields into the section.
                        section.push(WPJSFSP.templates.field(field));
                    });

                    // Push rendered sections into the form.
                    form += WPJSFSP.templates.section({
                        fields: section
                    });
                });
            }
            else {
                data.fields = forms.parseFields(data.fields, values);

                // Replace the array with rendered fields.
                _.each(data.fields, function (field) {
                    // Store the field by id for easy lookup later.
                    form_fields[field.id] = field;

                    // Push rendered fields into the section.
                    section.push(WPJSFSP.templates.field(field));
                });

                // Render the section.
                form = WPJSFSP.templates.section({
                    fields: section
                });
            }

            if ($container !== undefined && $container.length) {
                $container
                    .addClass(container_classes.join('  '))
                    .data('form_fields', form_fields)
                    .html(form)
                    .trigger('wpjsfsp_init');
            }

            return form;

        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.forms = forms;

    $(document)
        .on('wpjsfsp_init  wpjsfsp_form_check', function () {
            WPJSFSP.forms.init();
        })
        .on('wpjsfspFieldChanged', '.wpjsfsp-field', function () {
            var $wrapper            = $(this),
                dependent_field_ids = $wrapper.data('wpjsfsp-field-dependents') || [],
                $dependent_fields   = $(),
                i;

            if (!dependent_field_ids || dependent_field_ids.length <= 0) {
                return;
            }

            for (i = 0; i < dependent_field_ids.length; i++) {
                $dependent_fields = $dependent_fields.add('.wpjsfsp-field[data-id="' + dependent_field_ids[i] + '"]');
            }

            WPJSFSP.forms.checkDependencies($dependent_fields);
        })
        .on('wpjsfspFieldChanged', '.wpjsfsp-field-dynamic-desc', function () {
            var $this       = $(this),
                $input      = $this.find(':input'),
                $container  = $this.parents('.ahoy-dynamic-form:first'),
                val         = $input.val(),
                form_fields = $container.data('form_fields') || {},
                field       = form_fields[$this.data('id')] || {},
                $desc       = $this.find('.wpjsfsp-desc'),
                desc        = $this.data('wpjsfsp-dynamic-desc');

            switch (field.type) {
            case 'radio':
                val = $this.find(':input:checked').val();
                break;
            }

            field.value = val;

            if (desc && desc.length) {
                $desc.html(WPJSFSP.templates.renderInline(desc, field));
            }
        })
        .on('change', '.wpjsfsp-field-select select', function () {
            $(this).parents('.wpjsfsp-field').trigger('wpjsfspFieldChanged');
        })
        .on('click', '.wpjsfsp-field-checkbox input', function () {
            $(this).parents('.wpjsfsp-field').trigger('wpjsfspFieldChanged');
        })
        .on('click', '.wpjsfsp-field-radio input', function (event) {
            var $this     = $(this),
                $selected = $this.parents('li'),
                $wrapper  = $this.parents('.wpjsfsp-field');

            $wrapper.trigger('wpjsfspFieldChanged');

            $wrapper.find('li.wpjsfsp-selected').removeClass('wpjsfsp-selected');

            $selected.addClass('wpjsfsp-selected');
        });

}(jQuery));