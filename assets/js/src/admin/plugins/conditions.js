/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    var conditions = {
        get_conditions: function () {
            return window.wpjsfsp_settings_editor.conditions_selectlist;
        },
        not_operand_checkbox: function ($element) {

            $element = $element || $('.wpjsfsp-not-operand');

            return $element.each(function () {
                var $this  = $(this),
                    $input = $this.find('input');

                $input.prop('checked', !$input.is(':checked'));

                WPJSFSP.conditions.toggle_not_operand($this);
            });

        },
        toggle_not_operand: function ($element) {
            $element = $element || $('.wpjsfsp-not-operand');

            return $element.each(function () {
                var $this      = $(this),
                    $input     = $this.find('input'),
                    $is        = $this.find('.is'),
                    $not       = $this.find('.not'),
                    $container = $this.parents('.facet-target');

                if ($input.is(':checked')) {
                    $is.hide();
                    $not.show();
                    $container.addClass('not-operand-checked');
                } else {
                    $is.show();
                    $not.hide();
                    $container.removeClass('not-operand-checked');
                }
            });
        },
        template: {
            editor: function (args) {
                var data = $.extend(true, {}, {
                    groups: []
                }, args);

                data.groups = WPJSFSP.utils.object_to_array(data.groups);

                return WPJSFSP.templates.render('wpjsfsp-condition-editor', data);
            },
            group: function (args) {
                var data = $.extend(true, {}, {
                        index: '',
                        facets: []
                    }, args),
                    i;

                data.facets = WPJSFSP.utils.object_to_array(data.facets);

                for (i = 0; data.facets.length > i; i++) {
                    data.facets[i].index = i;
                    data.facets[i].group = data.index;
                }

                return WPJSFSP.templates.render('wpjsfsp-condition-group', data);
            },
            facet: function (args) {
                var data = $.extend(true, {}, {
                    group: '',
                    index: '',
                    target: '',
                    not_operand: false,
                    settings: {}
                }, args);

                return WPJSFSP.templates.render('wpjsfsp-condition-facet', data);
            },
            settings: function (args, values) {
                var fields = [],
                    data   = $.extend(true, {}, {
                        index: '',
                        group: '',
                        target: null,
                        fields: []
                    }, args);

                if (!data.fields.length && wpjsfsp_settings_editor.conditions[args.target] !== undefined) {
                    data.fields = wpjsfsp_settings_editor.conditions[args.target].fields;
                }

                if (undefined === values) {
                    values = {};
                }

                // Replace the array with rendered fields.
                _.each(data.fields, function (field, fieldID) {

                    field = WPJSFSP.models.field(field);

                    if (typeof field.meta !== 'object') {
                        field.meta = {};
                    }

                    if (undefined !== values[fieldID]) {
                        field.value = values[fieldID];
                    }

                    field.name = 'wpjsfsp_settings[conditions][' + data.group + '][' + data.index + '][settings][' + fieldID + ']';

                    if (field.id === '') {
                        field.id = 'conditions_' + data.group + '_' + data.index + '_settings_' + fieldID;
                    }

                    fields.push(WPJSFSP.templates.field(field));
                });

                // Render the section.
                return WPJSFSP.templates.section({
                    fields: fields
                });
            },
            selectbox: function (args) {
                var data = $.extend(true, {}, {
                    id: null,
                    name: null,
                    type: 'select',
                    group: '',
                    index: '',
                    value: null,
                    select2: true,
                    classes: ['facet-target', 'facet-select'],
                    options: WPJSFSP.conditions.get_conditions()
                }, args);

                if (data.id === null) {
                    data.id = 'conditions_' + data.group + '_' + data.index + '_target';
                }

                if (data.name === null) {
                    data.name = 'wpjsfsp_settings[conditions][' + data.group + '][' + data.index + '][target]';
                }

                return WPJSFSP.templates.field(data);
            }
        },
        groups: {
            add: function (editor, target, not_operand) {
                var $editor = $(editor),
                    data    = {
                        index: $editor.find('.facet-group-wrap').length,
                        facets: [
                            {
                                target: target || null,
                                not_operand: not_operand || false,
                                settings: {}
                            }
                        ]
                    };


                $editor.find('.facet-groups').append(WPJSFSP.conditions.template.group(data));
                $editor.addClass('has-conditions');
            },
            remove: function ($group) {
                var $editor = $group.parents('.facet-builder');

                $group.prev('.facet-group-wrap').find('.and .add-facet').removeClass('disabled');
                $group.remove();

                WPJSFSP.conditions.renumber();

                if ($editor.find('.facet-group-wrap').length === 0) {
                    $editor.removeClass('has-conditions');

                    $('#wpjsfsp-first-condition')
                        .val(null)
                        .trigger('change');
                }
            }
        },
        facets: {
            add: function ($group, target, not_operand) {
                var data = {
                    group: $group.data('index'),
                    index: $group.find('.facet').length,
                    target: target || null,
                    not_operand: not_operand || false,
                    settings: {}
                };

                $group.find('.facet-list').append(WPJSFSP.conditions.template.facet(data));
            },
            remove: function ($facet) {
                var $group = $facet.parents('.facet-group-wrap');

                $facet.remove();

                if ($group.find('.facet').length === 0) {
                    WPJSFSP.conditions.groups.remove($group);
                } else {
                    WPJSFSP.conditions.renumber();
                }
            }
        },
        renumber: function () {
            $('.facet-builder .facet-group-wrap').each(function () {
                var $group     = $(this),
                    groupIndex = $group.parent().children().index($group);

                $group
                    .data('index', groupIndex)
                    .find('.facet').each(function () {
                    var $facet     = $(this),
                        facetIndex = $facet.parent().children().index($facet);

                    $facet
                        .data('index', facetIndex)
                        .find('[name]').each(function () {
                        var replace_with = "wpjsfsp_settings[conditions][" + groupIndex + "][" + facetIndex + "]";
                        this.name = this.name.replace(/wpjsfsp_settings\[conditions\]\[\d*?\]\[\d*?\]/, replace_with);
                        this.id = this.name;
                    });
                });
            });
        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.conditions = conditions;

    $(document)
        .on('wpjsfsp_init', function () {
            WPJSFSP.conditions.renumber();
            WPJSFSP.conditions.toggle_not_operand();
        })
        .on('select2:select wpjsfselect2:select', '#wpjsfsp-first-condition', function (event) {
            var $field      = $(this),
                $editor     = $field.parents('.facet-builder').eq(0),
                target      = $field.val(),
                $operand    = $editor.find('#wpjsfsp-first-facet-operand'),
                not_operand = $operand.is(':checked');

            WPJSFSP.conditions.groups.add($editor, target, not_operand);

            $field
                .val(null)
                .trigger('change');

            $operand.prop('checked', false).parents('.facet-target').removeClass('not-operand-checked');
            $(document).trigger('wpjsfsp_init');
        })
        .on('click', '.facet-builder .wpjsfsp-not-operand', function () {
            WPJSFSP.conditions.not_operand_checkbox($(this));
        })
        .on('change', '.facet-builder .facet-target select', function (event) {
            var $this  = $(this),
                $facet = $this.parents('.facet'),
                target = $this.val(),
                data   = {
                    target: target
                };

            if (target === '' || target === $facet.data('target')) {
                return;
            }

            $facet.data('target', target).find('.facet-settings').html(WPJSFSP.conditions.template.settings(data));
            $(document).trigger('wpjsfsp_init');
        })
        .on('click', '.facet-builder .facet-group-wrap:last-child .and .add-facet', function () {
            WPJSFSP.conditions.groups.add($(this).parents('.facet-builder').eq(0));
            $(document).trigger('wpjsfsp_init');
        })
        .on('click', '.facet-builder .add-or .add-facet:not(.disabled)', function () {
            WPJSFSP.conditions.facets.add($(this).parents('.facet-group-wrap').eq(0));
            $(document).trigger('wpjsfsp_init');
        })
        .on('click', '.facet-builder .remove-facet', function () {
            WPJSFSP.conditions.facets.remove($(this).parents('.facet').eq(0));
            $(document).trigger('wpjsfsp_init');
        });

}(jQuery));