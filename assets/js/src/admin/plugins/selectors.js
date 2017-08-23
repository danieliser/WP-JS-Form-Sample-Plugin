/*
 * @copyright   Copyright (c) 2017, Code Atlantic
 * @author      Daniel Iser
 */
(function ($) {
    "use strict";

    function Selector_Cache() {
        var elementCache = {};

        var get_from_cache = function (selector, $ctxt, reset) {

            if ('boolean' === typeof $ctxt) {
                reset = $ctxt;
                $ctxt = false;
            }
            var cacheKey = $ctxt ? $ctxt.selector + ' ' + selector : selector;

            if (undefined === elementCache[cacheKey] || reset) {
                elementCache[cacheKey] = $ctxt ? $ctxt.find(selector) : jQuery(selector);
            }

            return elementCache[cacheKey];
        };

        get_from_cache.elementCache = elementCache;
        return get_from_cache;
    }

    var selectors = new Selector_Cache();

    // Import this module.
    window.WPJSFSP = window.WPJSFSP || {};
    window.WPJSFSP.selectors = selectors;
}(jQuery));