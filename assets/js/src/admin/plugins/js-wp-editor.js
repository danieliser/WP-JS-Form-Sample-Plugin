/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
var wpActiveEditor = true;
(function ($) {
    "use strict";

    var wp_editor = function (options) {

        var default_options,
            id_regexp = new RegExp('wpjsfsp_id', 'g');

        if (typeof tinyMCEPreInit === 'undefined' || typeof QTags === 'undefined' || typeof wpjsfsp_wpeditor_vars === 'undefined') {
            console.warn('js_wp_editor( $settings ); must be loaded');
            return this;
        }

        default_options = {
            'mode': 'html',
            'mceInit': {
                "theme": "modern",
                "skin": "lightgray",
                "language": "en",
                "formats": {
                    "alignleft": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "left"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["alignleft"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "aligncenter": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "center"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["aligncenter"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "alignright": [
                        {
                            "selector": "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li",
                            "styles": {"textAlign": "right"},
                            "deep": false,
                            "remove": "none"
                        },
                        {
                            "selector": "img,table,dl.wp-caption",
                            "classes": ["alignright"],
                            "deep": false,
                            "remove": "none"
                        }
                    ],
                    "strikethrough": {"inline": "del", "deep": true, "split": true}
                },
                "relative_urls": false,
                "remove_script_host": false,
                "convert_urls": false,
                "browser_spellcheck": true,
                "fix_list_elements": true,
                "entities": "38,amp,60,lt,62,gt",
                "entity_encoding": "raw",
                "keep_styles": false,
                "paste_webkit_styles": "font-weight font-style color",
                "preview_styles": "font-family font-size font-weight font-style text-decoration text-transform",
                "wpeditimage_disable_captions": false,
                "wpeditimage_html5_captions": false,
                "plugins": "charmap,hr,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpview,image",
                "content_css": wpjsfsp_wpeditor_vars.includes_url + "css/dashicons.css?ver=3.9," + wpjsfsp_wpeditor_vars.includes_url + "js/mediaelement/mediaelementplayer.min.css?ver=3.9," + wpjsfsp_wpeditor_vars.includes_url + "js/mediaelement/wp-mediaelement.css?ver=3.9," + wpjsfsp_wpeditor_vars.includes_url + "js/tinymce/skins/wordpress/wp-content.css?ver=3.9",
                "selector": "#wpjsfsp_id",
                "resize": "vertical",
                "menubar": false,
                "wpautop": true,
                "indent": false,
                "toolbar1": "bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
                "toolbar2": "formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
                "toolbar3": "",
                "toolbar4": "",
                "tabfocus_elements": ":prev,:next",
                "body_class": "wpjsfsp_id"
            }
        };

        if (tinyMCEPreInit.mceInit.wpjsfsp_id) {
            default_options.mceInit = tinyMCEPreInit.mceInit.wpjsfsp_id;
        }

        options = $.extend(true, {}, default_options, options);

        return this.each(function () {
            var $this      = $(this),
                current_id = $this.attr('id'),
                temp       = {};

            if (tinyMCE.editors[current_id] !== undefined) {
                tinyMCE.remove(tinymce.editors[current_id]);
            }

            if (!$this.is('textarea')) {
                console.warn('Element must be a textarea');
                if ($this.closest('.wp-editor-wrap').length) {
                    temp.editor_wrap = $this.closest('.wp-editor-wrap');
                    temp.field_parent = temp.editor_wrap.parent();

                    temp.editor_wrap.before($this.clone());
                    temp.editor_wrap.remove();

                    $this = temp.field_parent.find('textarea[id="' + current_id + '"]');
                }
            }
            $this.addClass('wp-editor-area').show();


            $.each(options.mceInit, function (key, value) {
                if ($.type(value) === 'string') {
                    options.mceInit[key] = value.replace(id_regexp, current_id);
                }
            });

            options.mode = options.mode === 'tmce' ? 'tmce' : 'html';

            tinyMCEPreInit.mceInit[current_id] = options.mceInit;

            var wrap                = $('<div id="wp-' + current_id + '-wrap" class="wp-core-ui wp-editor-wrap ' + options.mode + '-active" />'),
                editor_tools        = $('<div id="wp-' + current_id + '-editor-tools" class="wp-editor-tools hide-if-no-js" />'),
                editor_tabs         = $('<div class="wp-editor-tabs" />'),
                switch_editor_html  = $('<a id="' + current_id + '-html" class="wp-switch-editor switch-html" data-wp-editor-id="' + current_id + '">Text</a>'),
                switch_editor_tmce  = $('<a id="' + current_id + '-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="' + current_id + '">Visual</a>'),
                media_buttons       = $('<div id="wp-' + current_id + '-media-buttons" class="wp-media-buttons" />'),
                insert_media_button = $('<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="' + current_id + '" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>'),
                editor_container    = $('<div id="wp-' + current_id + '-editor-container" class="wp-editor-container" />'),
                content_css         = /*Object.prototype.hasOwnProperty.call(tinyMCEPreInit.mceInit[current_id], 'content_css') ? tinyMCEPreInit.mceInit[current_id]['content_css'].split(',') :*/ false;

            insert_media_button.appendTo(media_buttons);
            media_buttons.appendTo(editor_tools);

            switch_editor_html.appendTo(editor_tabs);
            switch_editor_tmce.appendTo(editor_tabs);
            editor_tabs.appendTo(editor_tools);

            editor_tools.appendTo(wrap);
            editor_container.appendTo(wrap);

            editor_container.append($this.clone().addClass('wp-editor-area'));

            if (content_css !== false)
                $.each(content_css, function () {
                    if (!$('link[href="' + this + '"]').length)
                        $this.before('<link rel="stylesheet" type="text/css" href="' + this + '">');
                });

            $this.before('<link rel="stylesheet" id="editor-buttons-css" href="' + wpjsfsp_wpeditor_vars.includes_url + 'css/editor.css" type="text/css" media="all">');

            $this.before(wrap);
            $this.remove();

            new QTags(current_id);
            QTags._buttonsInit();
            switchEditors.go(current_id, options.mode);

            $('.insert-media', wrap).on('click', function (event) {
                var elem    = $(event.currentTarget),
                    options = {
                        frame: 'post',
                        state: 'insert',
                        title: wp.media.view.l10n.addMedia,
                        multiple: true
                    };

                event.preventDefault();

                elem.blur();

                if (elem.hasClass('gallery')) {
                    options.state = 'gallery';
                    options.title = wp.media.view.l10n.createGalleryTitle;
                }

                wp.media.editor.open(current_id, options);
            });

        });
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.wp_editor = wp_editor;

    $.fn.wp_editor = WPJSFSP.wp_editor;

    $(document)
        .on('wpjsfsp_init', function () {

            $('.wpjsfsp-field-editor textarea:not(.initialized)').each(function () {
                var $this = $(this).addClass('initialized');
                $this.wp_editor({
                    mode: 'tmce'
                });
            });
        })
        .on('mousedown', '.wpjsfsp-submit button', function (event) {
            var $form = $(event.target).parents('form').eq(0);

            tinyMCE.triggerSave();

            $form.trigger('wpjsfsp_before_submit');
        });

}(jQuery));