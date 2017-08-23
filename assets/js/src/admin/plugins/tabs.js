/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";
    var tabs = {
        init: function () {
            $('.wpjsfsp-tabs-container').filter(':not(.wpjsfsp-tabs-initialized)').each(function () {
                var $this          = $(this).addClass('wpjsfsp-tabs-initialized'),
                    $tabList       = $this.find('> ul.tabs'),
                    $firstTab      = $tabList.find('> li:first'),
                    forceMinHeight = $this.data('min-height');

                if ($this.hasClass('vertical-tabs')) {
                    var minHeight = forceMinHeight && forceMinHeight > 0 ? forceMinHeight : $tabList.eq(0).outerHeight(true);

                    $this.css({
                        minHeight: minHeight + 'px'
                    });

                    if ($this.parent().innerHeight < minHeight) {
                        $this.parent().css({
                            minHeight: minHeight + 'px'
                        });
                    }
                }

                // Trigger first tab.
                $firstTab.trigger('click');
            });
        }
    };

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.tabs = tabs;

    $(document)
        .on('wpjsfsp_init', function () {
            WPJSFSP.tabs.init();
        })
        .on('click', '.wpjsfsp-tabs-initialized li.tab', function (e) {
            var $this         = $(this),
                $container    = $this.parents('.wpjsfsp-tabs-container:first'),
                $tabs         = $container.find('> ul.tabs > li.tab'),
                $tab_contents = $container.find('> div.tab-content'),
                link          = $this.find('a').attr('href');

            $tabs.removeClass('active');
            $tab_contents.removeClass('active');

            $this.addClass('active');
            $container.find('> div.tab-content' + link).addClass('active');

            e.preventDefault();
        });
}(jQuery));