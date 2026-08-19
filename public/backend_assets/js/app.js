/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
!function (e) {
    "function" == typeof define && define.amd ? define(["jquery"], e) : e("object" == typeof exports ? require("jquery") : jQuery)
}(function (e) {
    function n(e) {
        return u.raw ? e : encodeURIComponent(e)
    }

    function o(e) {
        return u.raw ? e : decodeURIComponent(e)
    }

    function i(e) {
        return n(u.json ? JSON.stringify(e) : String(e))
    }

    function r(e) {
        0 === e.indexOf('"') && (e = e.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
        try {
            return e = decodeURIComponent(e.replace(c, " ")), u.json ? JSON.parse(e) : e
        } catch (n) {
        }
    }

    function t(n, o) {
        var i = u.raw ? n : r(n);
        return e.isFunction(o) ? o(i) : i
    }

    var c = /\+/g, u = e.cookie = function (r, c, f) {
        if (void 0 !== c && !e.isFunction(c)) {
            if (f = e.extend({}, u.defaults, f), "number" == typeof f.expires) {
                var a = f.expires, d = f.expires = new Date;
                d.setTime(+d + 864e5 * a)
            }
            return document.cookie = [n(r), "=", i(c), f.expires ? "; expires=" + f.expires.toUTCString() : "", f.path ? "; path=" + f.path : "", f.domain ? "; domain=" + f.domain : "", f.secure ? "; secure" : ""].join("")
        }
        for (var p = r ? void 0 : {}, s = document.cookie ? document.cookie.split("; ") : [], m = 0, x = s.length; x > m; m++) {
            var v = s[m].split("="), k = o(v.shift()), l = v.join("=");
            if (r && r === k) {
                p = t(l, c);
                break
            }
            r || void 0 === (l = t(l)) || (p[k] = l)
        }
        return p
    };
    u.defaults = {}, e.removeCookie = function (n, o) {
        return void 0 === e.cookie(n) ? !1 : (e.cookie(n, "", e.extend({}, o, {expires: -1})), !e.cookie(n))
    }
});

$.extend(
    {
        context: function (fn, context) {
            return $.proxy(fn, context);
        }
    }
);

var App = window.App || {};

App = $.extend(
    {
        events: {
            DOMAdded: 'DOMAdded',
            DOMReady: 'ready',
            DOMRemoved: 'DOMRemoved',
        }
    }, App
);

App.ajax = function (url, data, callback, method, options) {
    if (!data['_token']) {
        data['_token'] = $('meta[name="csrf-token"]').attr('content');
    }

    var options = $.extend(
        {
            url: url,
            data: data,
            method: method || 'POST',
            dataType: 'json',
            timeout: 30000,
            success: function (response) {
                callback(response);
            },
            error: function (xhr) {
                callback(null);
            }
        }, options
    );

    return $.ajax(options);
};

;(function ($, window, document) {
    'use strict';

    jQuery.extend(true, {
        context: function (fn, context) {
            if (typeof context == 'string') {
                var _context = fn;
                fn = fn[context];
                context = _context;
            }

            return function () {
                return fn.apply(context, arguments);
            };
        }
    });

    var ducnhCMS = {
        _modal: false,
        _loading: false,
        _loadedScripts: {},
    };

    var AppRegistry = (function () {
        'use strict';

        var registeredComponents_ = {};
        var counter_ = 0;
        var registryAttr = 'registry';

        function register(selector, fn) {
            var selectElements = selector.split(','),
                totalSelector = selectElements.length,
                i;

            for (i = 0; i < totalSelector; i++) {
                var selectElement = selectElements[i].trim();
                if (!selectElement.length) {
                    continue;
                }

                registeredComponents_[selectElement] = fn;
                upgradeElementsBySelector(selectElement);
            }
        }

        function upgradeElementsBySelector(selector) {
            var $elements = $(document).find(selector),
                fn = registeredComponents_[selector];

            for (var i = 0; i < $elements.length; i++) {
                var element = $elements.get(i);
                var $el = $(element);

                if (isElementUpgraded_(element, selector)) {
                    continue;
                }

                ++counter_;
                element.setAttribute(registryAttr, 'Registry,' + selector + ',' + counter_);
                new fn($el);
            }
        }

        function upgradeDOM() {
            for (var selector in registeredComponents_) {
                upgradeElementsBySelector(selector);
            }
        }

        function getRegistryListOfElement_(element) {
            var registry = element.getAttribute(registryAttr);
            return (registry === null) ? [] : registry.split(',');
        }

        function isElementUpgraded_(element, jsClass) {
            var registryList = getRegistryListOfElement_(element);
            return registryList.indexOf(jsClass) !== -1;
        }

        return {
            register: register,
            upgradeDOM: upgradeDOM,
            upgradeBySelector: upgradeElementsBySelector
        };
    }());

    ducnhCMS.AppRegistry = AppRegistry;

    jQuery.extend(ducnhCMS, {
        init: function () {
            console.info('Begin application.');
            var collapsed = $.cookie('sidebar-collapsed') === 'true' ? true : false,
                $toggle = $('.sidebar-toggle');

            if (collapsed) {
                $('body').addClass('sidebar-collapse');
            }

            function toggleSidebar(open) {
                $.cookie('sidebar-collapsed', collapsed, {
                    expires: 7,
                    path: '/'
                });
            }

            $toggle.on('click', function (e) {
                collapsed = !collapsed;
                toggleSidebar(collapsed);
            });

            // run check unread notifications for every 30 seconds?
            this.pendingXhr;

            // clear input data
            $('.btn-clear_input_value').click(function (e) {
                e.preventDefault();
                var target_name = $(this).data('target_name');
                var target_id = $(this).data('target_id');

                if (typeof target_name == 'undefined' || typeof target_id == 'undefined') {
                    return false;
                }

                $('body').find(target_name).val('');
                $('body').find(target_id).val('');
            });

            $("body").on('click', '.checkall', function () {
                var checkbox = $('body').find('.chk');
                checkbox.prop('checked', this.checked);
            });

            $("body").on('click', '.chk', function () {
                var checkbox_checked = $('body').find('.chk:checked');
                var checkbox = $('body').find('.chk');
                var checkall = $('body').find('.checkall');
                checkall.prop('checked', (checkbox_checked.length == checkbox.length) ? true : false);
            });

        },

        showHideLoading: function (isHide) {
            if (this._loading === false) {
                this._loading = $('#page-loading-spinner');
            }

            isHide ? this._loading.hide() : this._loading.show();
        },
        ajax: function (url, data, success, options) {
            if (!String(url).length) {
                throw new Error('Invalid AJAX url!');
            }

            this.showHideLoading();

            var options = $.extend({
                url: url,
                data: Object(data),
                method: 'POST',
                dataType: 'json',
                timeout: 30000, // 30 seconds?,
                success: function (response, textStatus, xhr) {
                    success(response, textStatus);

                    AppRegistry.upgradeDOM();
                    ducnhCMS.showHideLoading(true);

                    $(document).trigger({
                        type: 'AJAXSuccess',
                        response: response,
                        textStatus: textStatus
                    });
                },
                error: function (xhr, textStatus, errorThrown) {
                    success(this, null, textStatus);

                    ducnhCMS.showHideLoading(true);

                    $(document).trigger({
                        type: 'AJAXError',
                        textStatus: textStatus
                    });
                }
            }, options);

            return $.ajax(options);
        }
    });

    ducnhCMS.MassiveRemoveRecords = function ($button) {
        this.__construct($button);
    };
    ducnhCMS.MassiveRemoveRecords.prototype = {
        __construct: function ($button) {
            this.$button = $button;
            this.$checker = $(this.$button.data('checker'));

            this.$button.bind('click', $.context(this, 'remove'));

            $(document).on('PaginateUpdated', function (e) {
                this.$checker = $(this.$button.data('checker'));
                this.events();
            }.bind(this));

            this.$counter = $('<span class="counter">0</span>');
            this.$counter.appendTo(this.$button);

            this.events();

            this.url = this.$button.data('url');
            if (!this.url || !this.$checker.length) {
                console.log('MassiveRemoveRecords required data-checker & data-url attributes.');
            }
        },

        events: function () {
            this.$counter.text(0);
            this.$checker.on('change', $.context(this, 'toggleChecker'));

            $('body').on('change', '.checkall', $.context(this, 'toggleChecker'));
        },

        toggleChecker: function () {
            var data = this._getCheckerValues();

            this.$counter.text(data.ids.length);
        },

        _getCheckerValues: function () {
            var ids = [], rows = [];
            this.$checker.each(function () {
                var checker = $(this), row;

                if (checker.is(':checked')) {
                    ids.push(checker.val());

                    row = checker.parent().parent().parent();
                    if (row.get(0).nodeName == 'TR') {
                        rows.push(row);
                    }
                }
            });

            return {
                ids: ids,
                rows: rows
            };
        },

        remove: function (e) {
            e.preventDefault();

            var data = this._getCheckerValues();
            // console.log(data);return;
            if (!data.ids.length) {
                return;
            }
            if (confirm('Bạn có chắc chắn muốn xóa bản ghi này không?')) {
                ducnhCMS.ajax(this.url, {ids: data.ids}, function (ajaxData) {
                    if (ajaxData.code == 200) {
                        for (var i = 0; i < data.rows.length; i++) {
                            // alert(data.rows[i]);
                            data.rows[i].hide().remove();
                        }

                        this.$counter.text(0);
                    }
                }.bind(this));
            }
        }
    };
    ducnhCMS.AutoComplete = function ($input) {
        this.__construct($input);
    };
    ducnhCMS.AutoComplete.prototype = {
        __construct: function ($input) {
            this.$input = $input;
            this.$inputVal = $($input.data('inputid'));

            this.xhr = false;
            this.activeIndex = -1;

            this.lastInputVal = this.$input.val();
            this.queueId = 0;

            this.options = {
                url: $input.data('acurl') || '',
                minLength: $input.data('minlength') || 2,
                method: $input.data('method') || 'post',
                query: $input.data('queryname') || 'q'
            };

            if (!this.$input.attr('placeholder')) {
                this.$input.attr('placeholder', 'Nhập từ khoá để tìm kiếm...');
            }

            if (!this.options.url.length) {
                throw new Error('Autocomplete input must have URL.');
            }

            // disable auto complete
            this.$input.attr('autocomplete', 'off');
            this.$input.bind(
                {
                    keyup: $.proxy(this, 'onKeyupHandler'),
                    keydown: $.proxy(this, 'onKeydownHandler'),
                    blur: $.proxy(this, 'onBlurHandler'),
                    focus: $.proxy(this, 'onFocusHandler')
                }
            );

            this.$loading = $('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
            this.$loading.insertAfter(this.$input);
            this.$input.parent()
                .addClass('autocomplete-wrap');
        },

        onBlurHandler: function (e) {
            this.$loading.hide();
            //this.$loading[0].MaterialSpinner.stop();

            if ($('.autocomplete-results:not(.mouseIn)').length > 0) {
                this.hideResults();
            }
        },

        onFocusHandler: function (e) {
            this.$loading.css(
                {
                    left: this.$input.width()
                }
            );

            this.$loading.show();
            // if (!this.$loading[0].MaterialSpinner) {
            //     // manual upgrade element if this not upgraded
            //     componentHandler.upgradeElement(this.$loading[0]);
            // }
            // this.$loading[0].MaterialSpinner.start();

            var value = this.$input.val();
            if (this.$input.val().length >= this.options.minLength
                && value !== this.lastInputVal
            ) {
                this.queueLoad();
            }
        },

        onKeyupHandler: function (e) {
            var value = this.$input.val();
            if (this.lastInputVal != value) {
                this.hideResults();
                this.$inputVal.val('');
                this.activeIndex = -1;

                if (value.length >= this.options.minLength) {
                    this.queueLoad();
                }
                this.lastInputVal = value;
            }
        },

        onKeydownHandler: function (e) {
            var keyCode = e.keyCode,
                $activeItem = false,
                $results = $('.autocomplete-results'),
                $items = $results.find('>li');

            if (keyCode == 39 || keyCode == 40) {
                this.activeIndex = Math.min($items.length - 1, this.activeIndex + 1);
            } else if (keyCode == 37 || keyCode == 38) {
                this.activeIndex = Math.max(0, this.activeIndex - 1);
            }

            for (var i = 0; i < $items.length; i++) {
                if (i == this.activeIndex) {
                    $($items[i]).addClass('active');
                    $activeItem = $($items[i]);
                } else {
                    $($items[i]).removeClass('active');
                }
            }

            if (keyCode == 13 && $activeItem && $activeItem.data('item')) {
                e.preventDefault();
                this.addValue($activeItem.data('item'));
            }
        },

        queueLoad: function () {
            if (this.queueId > 0) {
                clearTimeout(this.queueId);
                this.queueId = 0;
            }

            this.queueId = setTimeout($.proxy(this, 'load'), 250);
        },

        load: function () {
            this.queueId = 0;

            if (this.xhr) {
                this.xhr.abort();
                this.xhr = false;
            }

            var payload = this.options.params || {};
            payload[this.options.query] = this.$input.val();
            this.xhr = App.ajax(this.options.url, payload, $.proxy(this, 'onResponseHandler'), this.options.method);
        },

        onResponseHandler: function (response) {
            this.xhr = false;

            if (response != null) {
                var results = response.results || [];
                this.showResults(results);
            }
        },

        hideResults: function () {
            $('.autocomplete-results').remove();
        },

        showResults: function (results) {
            this.hideResults();

            var $ul = $('<ul />').addClass('autocomplete-results list-unstyled');
            if (results.length > 0) {
                for (var i = 0; i < results.length; i++) {
                    var $li = $(
                        '<li class="autocomplete-item">'
                        + '<p class="text-bold">' + results[i].label + '</p>'
                        + '<p class="hint">' + (results[i].hint || '') + '</p>'
                        + '</li>'
                    ).data('item', results[i]);

                    $li.appendTo($ul);
                    $li.bind('click', $.proxy(this, 'onItemClickHandler'));
                }
            } else {
                var $li = $('<li class="autocomplete-item"><p>Không có kết quả.</p></li>');
                $li.appendTo($ul);
            }

            $ul.appendTo('body');

            $ul.css(
                {
                    top: this.$input.offset().top + this.$input.outerHeight(),
                    left: this.$input.offset().left,
                    width: this.$input.outerWidth()
                }
            ).show();

            function onMouseEnter() {
                $ul.addClass('mouseIn');
            }

            function onMouseLeave() {
                $ul.removeClass('mouseIn');
            }

            $ul.bind(
                {
                    mouseenter: onMouseEnter,
                    mouseleave: onMouseLeave
                }
            );
        },

        onItemClickHandler: function (e) {
            var $item = $(e.currentTarget),
                data = $item.data('item') || {};

            if (!data.value) {
                return;
            }

            this.addValue(data);
        },

        addValue: function (data) {
            this.$input.val(data.label);
            this.lastInputVal = data.label;

            this.$inputVal.val(data.value).trigger('change');
            this.hideResults();
        }
    };

    window.ducnhCMS = ducnhCMS;
    window.AppRegistry = AppRegistry;
    ducnhCMS.init();

    AppRegistry.register('.MassiveRemoveRecords', ducnhCMS.MassiveRemoveRecords);
    AppRegistry.register('.auto-complete', ducnhCMS.AutoComplete);

}(jQuery, this, document));
