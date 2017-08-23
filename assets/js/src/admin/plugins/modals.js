/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    var $html                   = $('html'),
        $document               = $(document),
        $top_level_elements,
        focusableElementsString = "a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]",
        previouslyFocused,
        modals                  = {
            _current: null,
            // Accessibility: Checks focus events to ensure they stay inside the modal.
            forceFocus: function (event) {
                if (WPJSFSP.modals._current && !WPJSFSP.modals._current.contains(event.target)) {
                    event.stopPropagation();
                    WPJSFSP.modals._current.focus();
                }
            },
            trapEscapeKey: function (e) {
                if (e.keyCode === 27) {
                    WPJSFSP.modals.closeAll();
                    e.preventDefault();
                }
            },
            trapTabKey: function (e) {
                // if tab or shift-tab pressed
                if (e.keyCode === 9) {
                    // get list of focusable items
                    var focusableItems         = WPJSFSP.modals._current.find('*').filter(focusableElementsString).filter(':visible'),
                        // get currently focused item
                        focusedItem            = $(':focus'),
                        // get the number of focusable items
                        numberOfFocusableItems = focusableItems.length,
                        // get the index of the currently focused item
                        focusedItemIndex       = focusableItems.index(focusedItem);

                    if (e.shiftKey) {
                        //back tab
                        // if focused on first item and user preses back-tab, go to the last focusable item
                        if (focusedItemIndex === 0) {
                            focusableItems.get(numberOfFocusableItems - 1).focus();
                            e.preventDefault();
                        }
                    } else {
                        //forward tab
                        // if focused on the last item and user preses tab, go to the first focusable item
                        if (focusedItemIndex === numberOfFocusableItems - 1) {
                            focusableItems.get(0).focus();
                            e.preventDefault();
                        }
                    }
                }
            },
            setFocusToFirstItem: function () {
                // set focus to first focusable item
                WPJSFSP.modals._current.find('.wpjsfsp-modal-content *').filter(focusableElementsString).filter(':visible').first().focus();
            },
            closeAll: function (callback) {
                $('.wpjsfsp-modal-background')
                    .off('keydown.wpjsfsp_modal')
                    .hide(0, function () {
                        $('html').css({overflow: 'visible', width: 'auto'});

                        if ($top_level_elements) {
                            $top_level_elements.attr('aria-hidden', 'false');
                            $top_level_elements = null;
                        }

                        // Accessibility: Focus back on the previously focused element.
                        if (previouslyFocused.length) {
                            previouslyFocused.focus();
                        }

                        // Accessibility: Clears the WPJSFSP.modals._current var.
                        WPJSFSP.modals._current = null;

                        // Accessibility: Removes the force focus check.
                        $document.off('focus.wpjsfsp_modal');
                        if (undefined !== callback) {
                            callback();
                        }
                    })
                    .attr('aria-hidden', 'true');

            },
            show: function (modal, callback) {
                $('.wpjsfsp-modal-background')
                    .off('keydown.wpjsfsp_modal')
                    .hide(0)
                    .attr('aria-hidden', 'true');

                $html
                    .data('origwidth', $html.innerWidth())
                    .css({overflow: 'hidden', 'width': $html.innerWidth()});

                // Accessibility: Sets the previous focus element.

                var $focused = $(':focus');
                if (!$focused.parents('.wpjsfsp-modal-wrap').length) {
                    previouslyFocused = $focused;
                }

                // Accessibility: Sets the current modal for focus checks.
                WPJSFSP.modals._current = $(modal);

                // Accessibility: Close on esc press.
                WPJSFSP.modals._current
                    .on('keydown.wpjsfsp_modal', function (e) {
                        WPJSFSP.modals.trapEscapeKey(e);
                        WPJSFSP.modals.trapTabKey(e);
                    })
                    .show(0, function () {
                        $top_level_elements = $('body > *').filter(':visible').not(WPJSFSP.modals._current);
                        $top_level_elements.attr('aria-hidden', 'true');

                        WPJSFSP.modals._current
                            .trigger('wpjsfsp_init')
                            // Accessibility: Add focus check that prevents tabbing outside of modal.
                            .on('focus.wpjsfsp_modal', WPJSFSP.modals.forceFocus);

                        // Accessibility: Focus on the modal.
                        WPJSFSP.modals.setFocusToFirstItem();

                        if (undefined !== callback) {
                            callback();
                        }
                    })
                    .attr('aria-hidden', 'false');

            },
            remove: function (modal) {
                $(modal).remove();
            },
            replace: function (modal, replacement) {
                WPJSFSP.modals.remove($.trim(modal));
                $('body').append($.trim(replacement));
            },
            reload: function (modal, replacement, callback) {
                WPJSFSP.modals.replace(modal, replacement);
                WPJSFSP.modals.show(modal, callback);
                $(modal).trigger('wpjsfsp_init');
            }
        };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.modals = modals;

    $(document).on('click', '.wpjsfsp-modal-background, .wpjsfsp-modal-wrap .cancel, .wpjsfsp-modal-wrap .wpjsfsp-modal-close', function (e) {
        var $target = $(e.target);
        if (/*$target.hasClass('wpjsfsp-modal-background') || */$target.hasClass('cancel') || $target.hasClass('wpjsfsp-modal-close') || $target.hasClass('submitdelete')) {
            WPJSFSP.modals.closeAll();
            e.preventDefault();
            e.stopPropagation();
        }
    });

}(jQuery));