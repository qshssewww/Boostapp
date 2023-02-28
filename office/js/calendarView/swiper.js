/*!
 * jQuery Mobile <%= version %>
 * Git HEAD hash: f075f58e80e71014bbeb94dc0d2efd4cd800a0ba <> Date: Tue Jan 3 2017 12:51:34 UTC
 * http://jquerymobile.com
 *
 * Copyright 2010, 2017 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 */


(function (root, doc, factory) {
    if (typeof define === "function" && define.amd) {
        // AMD. Register as an anonymous module.
        define(["jquery"], function ($) {
            factory($, root, doc);
            return $.mobile;
        });
    } else {
        // Browser globals
        factory(root.jQuery, root, doc);
    }
}(this, document, function (jQuery, window, document, undefined) {
    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('jquery-ui/version', ["jquery"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    }(function ($) {

        $.ui = $.ui || {};

        return $.ui.version = "1.12.1";

    }));

    /*!
     * jQuery UI Widget 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Widget
//>>group: Core
//>>description: Provides a factory for creating stateful widgets with a common API.
//>>docs: http://api.jqueryui.com/jQuery.widget/
//>>demos: http://jqueryui.com/widget/

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('jquery-ui/widget', ["jquery", "./version"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    }(function ($) {

        var widgetUuid = 0;
        var widgetSlice = Array.prototype.slice;

        $.cleanData = (function (orig) {
            return function (elems) {
                var events, elem, i;
                for (i = 0; (elem = elems[ i ]) != null; i++) {
                    try {

                        // Only trigger remove when necessary to save time
                        events = $._data(elem, "events");
                        if (events && events.remove) {
                            $(elem).triggerHandler("remove");
                        }

                        // Http://bugs.jquery.com/ticket/8235
                    } catch (e) {
                    }
                }
                orig(elems);
            };
        })($.cleanData);

        $.widget = function (name, base, prototype) {
            var existingConstructor, constructor, basePrototype;

            // ProxiedPrototype allows the provided prototype to remain unmodified
            // so that it can be used as a mixin for multiple widgets (#8876)
            var proxiedPrototype = {};

            var namespace = name.split(".")[ 0 ];
            name = name.split(".")[ 1 ];
            var fullName = namespace + "-" + name;

            if (!prototype) {
                prototype = base;
                base = $.Widget;
            }

            if ($.isArray(prototype)) {
                prototype = $.extend.apply(null, [{}].concat(prototype));
            }

            // Create selector for plugin
            $.expr[ ":" ][ fullName.toLowerCase() ] = function (elem) {
                return !!$.data(elem, fullName);
            };

            $[ namespace ] = $[ namespace ] || {};
            existingConstructor = $[ namespace ][ name ];
            constructor = $[ namespace ][ name ] = function (options, element) {

                // Allow instantiation without "new" keyword
                if (!this._createWidget) {
                    return new constructor(options, element);
                }

                // Allow instantiation without initializing for simple inheritance
                // must use "new" keyword (the code above always passes args)
                if (arguments.length) {
                    this._createWidget(options, element);
                }
            };

            // Extend with the existing constructor to carry over any static properties
            $.extend(constructor, existingConstructor, {
                version: prototype.version,

                // Copy the object used to create the prototype in case we need to
                // redefine the widget later
                _proto: $.extend({}, prototype),

                // Track widgets that inherit from this widget in case this widget is
                // redefined after a widget inherits from it
                _childConstructors: []
            });

            basePrototype = new base();

            // We need to make the options hash a property directly on the new instance
            // otherwise we'll modify the options hash on the prototype that we're
            // inheriting from
            basePrototype.options = $.widget.extend({}, basePrototype.options);
            $.each(prototype, function (prop, value) {
                if (!$.isFunction(value)) {
                    proxiedPrototype[ prop ] = value;
                    return;
                }
                proxiedPrototype[ prop ] = (function () {
                    function _super() {
                        return base.prototype[ prop ].apply(this, arguments);
                    }

                    function _superApply(args) {
                        return base.prototype[ prop ].apply(this, args);
                    }

                    return function () {
                        var __super = this._super;
                        var __superApply = this._superApply;
                        var returnValue;

                        this._super = _super;
                        this._superApply = _superApply;

                        returnValue = value.apply(this, arguments);

                        this._super = __super;
                        this._superApply = __superApply;

                        return returnValue;
                    };
                })();
            });
            constructor.prototype = $.widget.extend(basePrototype, {

                // TODO: remove support for widgetEventPrefix
                // always use the name + a colon as the prefix, e.g., draggable:start
                // don't prefix for widgets that aren't DOM-based
                widgetEventPrefix: existingConstructor ? (basePrototype.widgetEventPrefix || name) : name
            }, proxiedPrototype, {
                constructor: constructor,
                namespace: namespace,
                widgetName: name,
                widgetFullName: fullName
            });

            // If this widget is being redefined then we need to find all widgets that
            // are inheriting from it and redefine all of them so that they inherit from
            // the new version of this widget. We're essentially trying to replace one
            // level in the prototype chain.
            if (existingConstructor) {
                $.each(existingConstructor._childConstructors, function (i, child) {
                    var childPrototype = child.prototype;

                    // Redefine the child widget using the same prototype that was
                    // originally used, but inherit from the new version of the base
                    $.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor,
                            child._proto);
                });

                // Remove the list of existing child constructors from the old constructor
                // so the old child constructors can be garbage collected
                delete existingConstructor._childConstructors;
            } else {
                base._childConstructors.push(constructor);
            }

            $.widget.bridge(name, constructor);

            return constructor;
        };

        $.widget.extend = function (target) {
            var input = widgetSlice.call(arguments, 1);
            var inputIndex = 0;
            var inputLength = input.length;
            var key;
            var value;

            for (; inputIndex < inputLength; inputIndex++) {
                for (key in input[ inputIndex ]) {
                    value = input[ inputIndex ][ key ];
                    if (input[ inputIndex ].hasOwnProperty(key) && value !== undefined) {

                        // Clone objects
                        if ($.isPlainObject(value)) {
                            target[ key ] = $.isPlainObject(target[ key ]) ?
                                    $.widget.extend({}, target[ key ], value) :
                                    // Don't extend strings, arrays, etc. with objects
                                    $.widget.extend({}, value);

                            // Copy everything else by reference
                        } else {
                            target[ key ] = value;
                        }
                    }
                }
            }
            return target;
        };

        $.widget.bridge = function (name, object) {
            var fullName = object.prototype.widgetFullName || name;
            $.fn[ name ] = function (options) {
                var isMethodCall = typeof options === "string";
                var args = widgetSlice.call(arguments, 1);
                var returnValue = this;

                if (isMethodCall) {

                    // If this is an empty collection, we need to have the instance method
                    // return undefined instead of the jQuery instance
                    if (!this.length && options === "instance") {
                        returnValue = undefined;
                    } else {
                        this.each(function () {
                            var methodValue;
                            var instance = $.data(this, fullName);

                            if (options === "instance") {
                                returnValue = instance;
                                return false;
                            }

                            if (!instance) {
                                return $.error("cannot call methods on " + name +
                                        " prior to initialization; " +
                                        "attempted to call method '" + options + "'");
                            }

                            if (!$.isFunction(instance[ options ]) || options.charAt(0) === "_") {
                                return $.error("no such method '" + options + "' for " + name +
                                        " widget instance");
                            }

                            methodValue = instance[ options ].apply(instance, args);

                            if (methodValue !== instance && methodValue !== undefined) {
                                returnValue = methodValue && methodValue.jquery ?
                                        returnValue.pushStack(methodValue.get()) :
                                        methodValue;
                                return false;
                            }
                        });
                    }
                } else {

                    // Allow multiple hashes to be passed on init
                    if (args.length) {
                        options = $.widget.extend.apply(null, [options].concat(args));
                    }

                    this.each(function () {
                        var instance = $.data(this, fullName);
                        if (instance) {
                            instance.option(options || {});
                            if (instance._init) {
                                instance._init();
                            }
                        } else {
                            $.data(this, fullName, new object(options, this));
                        }
                    });
                }

                return returnValue;
            };
        };

        $.Widget = function ( /* options, element */ ) {};
        $.Widget._childConstructors = [];

        $.Widget.prototype = {
            widgetName: "widget",
            widgetEventPrefix: "",
            defaultElement: "<div>",

            options: {
                classes: {},
                disabled: false,

                // Callbacks
                create: null
            },

            _createWidget: function (options, element) {
                element = $(element || this.defaultElement || this)[ 0 ];
                this.element = $(element);
                this.uuid = widgetUuid++;
                this.eventNamespace = "." + this.widgetName + this.uuid;

                this.bindings = $();
                this.hoverable = $();
                this.focusable = $();
                this.classesElementLookup = {};

                if (element !== this) {
                    $.data(element, this.widgetFullName, this);
                    this._on(true, this.element, {
                        remove: function (event) {
                            if (event.target === element) {
                                this.destroy();
                            }
                        }
                    });
                    this.document = $(element.style ?
                            // Element within the document
                            element.ownerDocument :
                            // Element is window or document
                            element.document || element);
                    this.window = $(this.document[ 0 ].defaultView || this.document[ 0 ].parentWindow);
                }

                this.options = $.widget.extend({},
                        this.options,
                        this._getCreateOptions(),
                        options);

                this._create();

                if (this.options.disabled) {
                    this._setOptionDisabled(this.options.disabled);
                }

                this._trigger("create", null, this._getCreateEventData());
                this._init();
            },

            _getCreateOptions: function () {
                return {};
            },

            _getCreateEventData: $.noop,

            _create: $.noop,

            _init: $.noop,

            destroy: function () {
                var that = this;

                this._destroy();
                $.each(this.classesElementLookup, function (key, value) {
                    that._removeClass(value, key);
                });

                // We can probably remove the unbind calls in 2.0
                // all event bindings should go through this._on()
                this.element
                        .off(this.eventNamespace)
                        .removeData(this.widgetFullName);
                this.widget()
                        .off(this.eventNamespace)
                        .removeAttr("aria-disabled");

                // Clean up events and states
                this.bindings.off(this.eventNamespace);
            },

            _destroy: $.noop,

            widget: function () {
                return this.element;
            },

            option: function (key, value) {
                var options = key;
                var parts;
                var curOption;
                var i;

                if (arguments.length === 0) {

                    // Don't return a reference to the internal hash
                    return $.widget.extend({}, this.options);
                }

                if (typeof key === "string") {

                    // Handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
                    options = {};
                    parts = key.split(".");
                    key = parts.shift();
                    if (parts.length) {
                        curOption = options[ key ] = $.widget.extend({}, this.options[ key ]);
                        for (i = 0; i < parts.length - 1; i++) {
                            curOption[ parts[ i ] ] = curOption[ parts[ i ] ] || {};
                            curOption = curOption[ parts[ i ] ];
                        }
                        key = parts.pop();
                        if (arguments.length === 1) {
                            return curOption[ key ] === undefined ? null : curOption[ key ];
                        }
                        curOption[ key ] = value;
                    } else {
                        if (arguments.length === 1) {
                            return this.options[ key ] === undefined ? null : this.options[ key ];
                        }
                        options[ key ] = value;
                    }
                }

                this._setOptions(options);

                return this;
            },

            _setOptions: function (options) {
                var key;

                for (key in options) {
                    this._setOption(key, options[ key ]);
                }

                return this;
            },

            _setOption: function (key, value) {
                if (key === "classes") {
                    this._setOptionClasses(value);
                }

                this.options[ key ] = value;

                if (key === "disabled") {
                    this._setOptionDisabled(value);
                }

                return this;
            },

            _setOptionClasses: function (value) {
                var classKey, elements, currentElements;

                for (classKey in value) {
                    currentElements = this.classesElementLookup[ classKey ];
                    if (value[ classKey ] === this.options.classes[ classKey ] ||
                            !currentElements ||
                            !currentElements.length) {
                        continue;
                    }

                    // We are doing this to create a new jQuery object because the _removeClass() call
                    // on the next line is going to destroy the reference to the current elements being
                    // tracked. We need to save a copy of this collection so that we can add the new classes
                    // below.
                    elements = $(currentElements.get());
                    this._removeClass(currentElements, classKey);

                    // We don't use _addClass() here, because that uses this.options.classes
                    // for generating the string of classes. We want to use the value passed in from
                    // _setOption(), this is the new value of the classes option which was passed to
                    // _setOption(). We pass this value directly to _classes().
                    elements.addClass(this._classes({
                        element: elements,
                        keys: classKey,
                        classes: value,
                        add: true
                    }));
                }
            },

            _setOptionDisabled: function (value) {
                this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value);

                // If the widget is becoming disabled, then nothing is interactive
                if (value) {
                    this._removeClass(this.hoverable, null, "ui-state-hover");
                    this._removeClass(this.focusable, null, "ui-state-focus");
                }
            },

            enable: function () {
                return this._setOptions({disabled: false});
            },

            disable: function () {
                return this._setOptions({disabled: true});
            },

            _classes: function (options) {
                var full = [];
                var that = this;

                options = $.extend({
                    element: this.element,
                    classes: this.options.classes || {}
                }, options);

                function processClassString(classes, checkOption) {
                    var current, i;
                    for (i = 0; i < classes.length; i++) {
                        current = that.classesElementLookup[ classes[ i ] ] || $();
                        if (options.add) {
                            current = $($.unique(current.get().concat(options.element.get())));
                        } else {
                            current = $(current.not(options.element).get());
                        }
                        that.classesElementLookup[ classes[ i ] ] = current;
                        full.push(classes[ i ]);
                        if (checkOption && options.classes[ classes[ i ] ]) {
                            full.push(options.classes[ classes[ i ] ]);
                        }
                    }
                }

                this._on(options.element, {
                    "remove": "_untrackClassesElement"
                });

                if (options.keys) {
                    processClassString(options.keys.match(/\S+/g) || [], true);
                }
                if (options.extra) {
                    processClassString(options.extra.match(/\S+/g) || []);
                }

                return full.join(" ");
            },

            _untrackClassesElement: function (event) {
                var that = this;
                $.each(that.classesElementLookup, function (key, value) {
                    if ($.inArray(event.target, value) !== -1) {
                        that.classesElementLookup[ key ] = $(value.not(event.target).get());
                    }
                });
            },

            _removeClass: function (element, keys, extra) {
                return this._toggleClass(element, keys, extra, false);
            },

            _addClass: function (element, keys, extra) {
                return this._toggleClass(element, keys, extra, true);
            },

            _toggleClass: function (element, keys, extra, add) {
                add = (typeof add === "boolean") ? add : extra;
                var shift = (typeof element === "string" || element === null),
                        options = {
                            extra: shift ? keys : extra,
                            keys: shift ? element : keys,
                            element: shift ? this.element : element,
                            add: add
                        };
                options.element.toggleClass(this._classes(options), add);
                return this;
            },

            _on: function (suppressDisabledCheck, element, handlers) {
                var delegateElement;
                var instance = this;

                // No suppressDisabledCheck flag, shuffle arguments
                if (typeof suppressDisabledCheck !== "boolean") {
                    handlers = element;
                    element = suppressDisabledCheck;
                    suppressDisabledCheck = false;
                }

                // No element argument, shuffle and use this.element
                if (!handlers) {
                    handlers = element;
                    element = this.element;
                    delegateElement = this.widget();
                } else {
                    element = delegateElement = $(element);
                    this.bindings = this.bindings.add(element);
                }

                $.each(handlers, function (event, handler) {
                    function handlerProxy() {

                        // Allow widgets to customize the disabled handling
                        // - disabled as an array instead of boolean
                        // - disabled class as method for disabling individual parts
                        if (!suppressDisabledCheck &&
                                (instance.options.disabled === true ||
                                        $(this).hasClass("ui-state-disabled"))) {
                            return;
                        }
                        return (typeof handler === "string" ? instance[ handler ] : handler)
                                .apply(instance, arguments);
                    }

                    // Copy the guid so direct unbinding works
                    if (typeof handler !== "string") {
                        handlerProxy.guid = handler.guid =
                                handler.guid || handlerProxy.guid || $.guid++;
                    }

                    var match = event.match(/^([\w:-]*)\s*(.*)$/);
                    var eventName = match[ 1 ] + instance.eventNamespace;
                    var selector = match[ 2 ];

                    if (selector) {
                        delegateElement.on(eventName, selector, handlerProxy);
                    } else {
                        element.on(eventName, handlerProxy);
                    }
                });
            },

            _off: function (element, eventName) {
                eventName = (eventName || "").split(" ").join(this.eventNamespace + " ") +
                        this.eventNamespace;
                element.off(eventName).off(eventName);

                // Clear the stack to avoid memory leaks (#10056)
                this.bindings = $(this.bindings.not(element).get());
                this.focusable = $(this.focusable.not(element).get());
                this.hoverable = $(this.hoverable.not(element).get());
            },

            _delay: function (handler, delay) {
                function handlerProxy() {
                    return (typeof handler === "string" ? instance[ handler ] : handler)
                            .apply(instance, arguments);
                }
                var instance = this;
                return setTimeout(handlerProxy, delay || 0);
            },

            _hoverable: function (element) {
                this.hoverable = this.hoverable.add(element);
                this._on(element, {
                    mouseenter: function (event) {
                        this._addClass($(event.currentTarget), null, "ui-state-hover");
                    },
                    mouseleave: function (event) {
                        this._removeClass($(event.currentTarget), null, "ui-state-hover");
                    }
                });
            },

            _focusable: function (element) {
                this.focusable = this.focusable.add(element);
                this._on(element, {
                    focusin: function (event) {
                        this._addClass($(event.currentTarget), null, "ui-state-focus");
                    },
                    focusout: function (event) {
                        this._removeClass($(event.currentTarget), null, "ui-state-focus");
                    }
                });
            },

            _trigger: function (type, event, data) {
                var prop, orig;
                var callback = this.options[ type ];

                data = data || {};
                event = $.Event(event);
                event.type = (type === this.widgetEventPrefix ?
                        type :
                        this.widgetEventPrefix + type).toLowerCase();

                // The original event may come from any element
                // so we need to reset the target on the new event
                event.target = this.element[ 0 ];

                // Copy original event properties over to the new event
                orig = event.originalEvent;
                if (orig) {
                    for (prop in orig) {
                        if (!(prop in event)) {
                            event[ prop ] = orig[ prop ];
                        }
                    }
                }

                this.element.trigger(event, data);
                return !($.isFunction(callback) &&
                        callback.apply(this.element[ 0 ], [event].concat(data)) === false ||
                        event.isDefaultPrevented());
            }
        };

        $.each({show: "fadeIn", hide: "fadeOut"}, function (method, defaultEffect) {
            $.Widget.prototype[ "_" + method ] = function (element, options, callback) {
                if (typeof options === "string") {
                    options = {effect: options};
                }

                var hasOptions;
                var effectName = !options ?
                        method :
                        options === true || typeof options === "number" ?
                        defaultEffect :
                        options.effect || defaultEffect;

                options = options || {};
                if (typeof options === "number") {
                    options = {duration: options};
                }

                hasOptions = !$.isEmptyObject(options);
                options.complete = callback;

                if (options.delay) {
                    element.delay(options.delay);
                }

                if (hasOptions && $.effects && $.effects.effect[ effectName ]) {
                    element[ method ](options);
                } else if (effectName !== method && element[ effectName ]) {
                    element[ effectName ](options.duration, options.easing, callback);
                } else {
                    element.queue(function (next) {
                        $(this)[ method ]();
                        if (callback) {
                            callback.call(element[ 0 ]);
                        }
                        next();
                    });
                }
            };
        });

        return $.widget;

    }));

    /*!
     * jQuery Mobile Namespace @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Namespace
//>>group: Core
//>>description: The mobile namespace on the jQuery object

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('ns', ["jquery"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        $.mobile = {version: "@VERSION"};

        return $.mobile;
    });

    /*!
     * jQuery UI Keycode 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Keycode
//>>group: Core
//>>description: Provide keycodes as keynames
//>>docs: http://api.jqueryui.com/jQuery.ui.keyCode/

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('jquery-ui/keycode', ["jquery", "./version"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    }(function ($) {
        return $.ui.keyCode = {
            BACKSPACE: 8,
            COMMA: 188,
            DELETE: 46,
            DOWN: 40,
            END: 35,
            ENTER: 13,
            ESCAPE: 27,
            HOME: 36,
            LEFT: 37,
            PAGE_DOWN: 34,
            PAGE_UP: 33,
            PERIOD: 190,
            RIGHT: 39,
            SPACE: 32,
            TAB: 9,
            UP: 38
        };

    }));

    /*!
     * jQuery Mobile Helpers @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Helpers
//>>group: Core
//>>description: Helper functions and references
//>>css.structure: ../css/structure/jquery.mobile.core.css
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('helpers', [
                "jquery",
                "./ns",
                "jquery-ui/keycode"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

// Subtract the height of external toolbars from the page height, if the page does not have
// internal toolbars of the same type. We take care to use the widget options if we find a
// widget instance and the element's data-attributes otherwise.
        var compensateToolbars = function (page, desiredHeight) {
            var pageParent = page.parent(),
                    toolbarsAffectingHeight = [],
                    // We use this function to filter fixed toolbars with option updatePagePadding set to
                    // true (which is the default) from our height subtraction, because fixed toolbars with
                    // option updatePagePadding set to true compensate for their presence by adding padding
                    // to the active page. We want to avoid double-counting by also subtracting their
                    // height from the desired page height.
                    noPadders = function () {
                        var theElement = $(this),
                                widgetOptions = $.mobile.toolbar && theElement.data("mobile-toolbar") ?
                                theElement.toolbar("option") : {
                            position: theElement.attr("data-" + $.mobile.ns + "position"),
                            updatePagePadding: (theElement.attr("data-" + $.mobile.ns +
                                    "update-page-padding") !== false)
                        };

                        return !(widgetOptions.position === "fixed" &&
                                widgetOptions.updatePagePadding === true);
                    },
                    externalHeaders = pageParent.children(":jqmData(type='header')").filter(noPadders),
                    internalHeaders = page.children(":jqmData(type='header')"),
                    externalFooters = pageParent.children(":jqmData(type='footer')").filter(noPadders),
                    internalFooters = page.children(":jqmData(type='footer')");

            // If we have no internal headers, but we do have external headers, then their height
            // reduces the page height
            if (internalHeaders.length === 0 && externalHeaders.length > 0) {
                toolbarsAffectingHeight = toolbarsAffectingHeight.concat(externalHeaders.toArray());
            }

            // If we have no internal footers, but we do have external footers, then their height
            // reduces the page height
            if (internalFooters.length === 0 && externalFooters.length > 0) {
                toolbarsAffectingHeight = toolbarsAffectingHeight.concat(externalFooters.toArray());
            }

            $.each(toolbarsAffectingHeight, function (index, value) {
                desiredHeight -= $(value).outerHeight();
            });

            // Height must be at least zero
            return Math.max(0, desiredHeight);
        };

        $.extend($.mobile, {
            // define the window and the document objects
            window: $(window),
            document: $(document),

            // TODO: Remove and use $.ui.keyCode directly
            keyCode: $.ui.keyCode,

            // Place to store various widget extensions
            behaviors: {},

            // Custom logic for giving focus to a page
            focusPage: function (page) {

                // First, look for an element explicitly marked for page focus
                var focusElement = page.find("[autofocus]");

                // If we do not find an element with the "autofocus" attribute, look for the page title
                if (!focusElement.length) {
                    focusElement = page.find(".ui-title").eq(0);
                }

                // Finally, fall back to focusing the page itself
                if (!focusElement.length) {
                    focusElement = page;
                }

                focusElement.focus();
            },

            // Scroll page vertically: scroll to 0 to hide iOS address bar, or pass a Y value
            silentScroll: function (ypos) {

                // If user has already scrolled then do nothing
                if ($.mobile.window.scrollTop() > 0) {
                    return;
                }

                if ($.type(ypos) !== "number") {
                    ypos = $.mobile.defaultHomeScroll;
                }

                // prevent scrollstart and scrollstop events
                $.event.special.scrollstart.enabled = false;

                setTimeout(function () {
                    window.scrollTo(0, ypos);
                    $.mobile.document.trigger("silentscroll", {x: 0, y: ypos});
                }, 20);

                setTimeout(function () {
                    $.event.special.scrollstart.enabled = true;
                }, 150);
            },

            getClosestBaseUrl: function (ele) {
                // Find the closest page and extract out its url.
                var url = $(ele).closest(".ui-page").jqmData("url"),
                        base = $.mobile.path.documentBase.hrefNoHash;

                if (!$.mobile.base.dynamicBaseEnabled || !url || !$.mobile.path.isPath(url)) {
                    url = base;
                }

                return $.mobile.path.makeUrlAbsolute(url, base);
            },
            removeActiveLinkClass: function (forceRemoval) {
                if (!!$.mobile.activeClickedLink &&
                        (!$.mobile.activeClickedLink.closest(".ui-page-active").length ||
                                forceRemoval)) {

                    $.mobile.activeClickedLink.removeClass("ui-button-active");
                }
                $.mobile.activeClickedLink = null;
            },

            enhanceable: function (elements) {
                return this.haveParents(elements, "enhance");
            },

            hijackable: function (elements) {
                return this.haveParents(elements, "ajax");
            },

            haveParents: function (elements, attr) {
                if (!$.mobile.ignoreContentEnabled) {
                    return elements;
                }

                var count = elements.length,
                        $newSet = $(),
                        e, $element, excluded,
                        i, c;

                for (i = 0; i < count; i++) {
                    $element = elements.eq(i);
                    excluded = false;
                    e = elements[ i ];

                    while (e) {
                        c = e.getAttribute ? e.getAttribute("data-" + $.mobile.ns + attr) : "";

                        if (c === "false") {
                            excluded = true;
                            break;
                        }

                        e = e.parentNode;
                    }

                    if (!excluded) {
                        $newSet = $newSet.add($element);
                    }
                }

                return $newSet;
            },

            getScreenHeight: function () {
                // Native innerHeight returns more accurate value for this across platforms,
                // jQuery version is here as a normalized fallback for platforms like Symbian
                return window.innerHeight || $.mobile.window.height();
            },

            //simply set the active page's minimum height to screen height, depending on orientation
            resetActivePageHeight: function (height) {
                var page = $(".ui-page-active"),
                        pageHeight = page.height(),
                        pageOuterHeight = page.outerHeight(true);

                height = compensateToolbars(page,
                        (typeof height === "number") ? height : $(window).height());

                // Remove any previous min-height setting
                page.css("min-height", "");

                // Set the minimum height only if the height as determined by CSS is insufficient
                if (page.height() < height) {
                    page.css("min-height", height - (pageOuterHeight - pageHeight));
                }
            },

            loading: function () {
                // If this is the first call to this function, instantiate a loader widget
                var loader = this.loading._widget || $.mobile.loader().element,
                        // Call the appropriate method on the loader
                        returnValue = loader.loader.apply(loader, arguments);

                // Make sure the loader is retained for future calls to this function.
                this.loading._widget = loader;

                return returnValue;
            },

            isElementCurrentlyVisible: function (el) {
                el = typeof el === "string" ? $(el)[ 0 ] : el[ 0 ];

                if (!el) {
                    return true;
                }

                var rect = el.getBoundingClientRect();

                return (
                        rect.bottom > 0 &&
                        rect.right > 0 &&
                        rect.top <
                        (window.innerHeight || document.documentElement.clientHeight) &&
                        rect.left <
                        (window.innerWidth || document.documentElement.clientWidth));
            }
        });

        $.addDependents = function (elem, newDependents) {
            var $elem = $(elem),
                    dependents = $elem.jqmData("dependents") || $();

            $elem.jqmData("dependents", $(dependents).add(newDependents));
        };

// plugins
        $.fn.extend({
            removeWithDependents: function () {
                $.removeWithDependents(this);
            },

            addDependents: function (newDependents) {
                $.addDependents(this, newDependents);
            },

            // note that this helper doesn't attempt to handle the callback
            // or setting of an html element's text, its only purpose is
            // to return the html encoded version of the text in all cases. (thus the name)
            getEncodedText: function () {
                return $("<a>").text(this.text()).html();
            },

            // fluent helper function for the mobile namespaced equivalent
            jqmEnhanceable: function () {
                return $.mobile.enhanceable(this);
            },

            jqmHijackable: function () {
                return $.mobile.hijackable(this);
            }
        });

        $.removeWithDependents = function (nativeElement) {
            var element = $(nativeElement);

            (element.jqmData("dependents") || $()).remove();
            element.remove();
        };
        $.addDependents = function (nativeElement, newDependents) {
            var element = $(nativeElement),
                    dependents = element.jqmData("dependents") || $();

            element.jqmData("dependents", $(dependents).add(newDependents));
        };

        $.find.matches = function (expr, set) {
            return $.find(expr, null, null, set);
        };

        $.find.matchesSelector = function (node, expr) {
            return $.find(expr, null, null, [node]).length > 0;
        };

        return $.mobile;
    });

    /*!
     * jQuery Mobile Defaults @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Defaults
//>>group: Core
//>>description: Default values for jQuery Mobile
//>>css.structure: ../css/structure/jquery.mobile.core.css
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('defaults', [
                "jquery",
                "./ns"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        return $.extend($.mobile, {

            hideUrlBar: true,

            // Keepnative Selector
            keepNative: ":jqmData(role='none'), :jqmData(role='nojs')",

            // Automatically handle clicks and form submissions through Ajax, when same-domain
            ajaxEnabled: true,

            // Automatically load and show pages based on location.hash
            hashListeningEnabled: true,

            // disable to prevent jquery from bothering with links
            linkBindingEnabled: true,

            // Set default page transition - 'none' for no transitions
            defaultPageTransition: "fade",

            // Set maximum window width for transitions to apply - 'false' for no limit
            maxTransitionWidth: false,

            // Set default dialog transition - 'none' for no transitions
            defaultDialogTransition: "pop",

            // Error response message - appears when an Ajax page request fails
            pageLoadErrorMessage: "Error Loading Page",

            // For error messages, which theme does the box use?
            pageLoadErrorMessageTheme: "a",

            // replace calls to window.history.back with phonegaps navigation helper
            // where it is provided on the window object
            phonegapNavigationEnabled: false,

            //automatically initialize the DOM when it's ready
            autoInitializePage: true,

            pushStateEnabled: true,

            // allows users to opt in to ignoring content by marking a parent element as
            // data-ignored
            ignoreContentEnabled: false,

            // default the property to remove dependency on assignment in init module
            pageContainer: $(),

            //enable cross-domain page support
            allowCrossDomainPages: false,

            dialogHashKey: "&ui-state=dialog"
        });
    });

    /*!
     * jQuery Mobile Data @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: jqmData
//>>group: Core
//>>description: Mobile versions of Data functions to allow for namespaceing
//>>css.structure: ../css/structure/jquery.mobile.core.css
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('data', [
                "jquery",
                "./ns"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        var nsNormalizeDict = {},
                oldFind = $.find,
                rbrace = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/,
                jqmDataRE = /:jqmData\(([^)]*)\)/g;

        $.extend($.mobile, {

            // Namespace used framework-wide for data-attrs. Default is no namespace

            ns: $.mobileBackcompat === false ? "ui-" : "",

            // Retrieve an attribute from an element and perform some massaging of the value

            getAttribute: function (element, key) {
                var data;

                element = element.jquery ? element[ 0 ] : element;

                if (element && element.getAttribute) {
                    data = element.getAttribute("data-" + $.mobile.ns + key);
                }

                // Copied from core's src/data.js:dataAttr()
                // Convert from a string to a proper data type
                try {
                    data = data === "true" ? true :
                            data === "false" ? false :
                            data === "null" ? null :
                            // Only convert to a number if it doesn't change the string
                            +data + "" === data ? +data :
                            rbrace.test(data) ? window.JSON.parse(data) :
                            data;
                } catch (err) {
                }

                return data;
            },

            // Expose our cache for testing purposes.
            nsNormalizeDict: nsNormalizeDict,

            // Take a data attribute property, prepend the namespace
            // and then camel case the attribute string. Add the result
            // to our nsNormalizeDict so we don't have to do this again.
            nsNormalize: function (prop) {
                return nsNormalizeDict[ prop ] ||
                        (nsNormalizeDict[ prop ] = $.camelCase($.mobile.ns + prop));
            },

            // Find the closest javascript page element to gather settings data jsperf test
            // http://jsperf.com/single-complex-selector-vs-many-complex-selectors/edit
            // possibly naive, but it shows that the parsing overhead for *just* the page selector vs
            // the page and dialog selector is negligable. This could probably be speed up by
            // doing a similar parent node traversal to the one found in the inherited theme code above
            closestPageData: function ($target) {
                return $target
                        .closest(":jqmData(role='page'), :jqmData(role='dialog')")
                        .data("mobile-page");
            }

        });

// Mobile version of data and removeData and hasData methods
// ensures all data is set and retrieved using jQuery Mobile's data namespace
        $.fn.jqmData = function (prop, value) {
            var result;
            if (typeof prop !== "undefined") {
                if (prop) {
                    prop = $.mobile.nsNormalize(prop);
                }

                // undefined is permitted as an explicit input for the second param
                // in this case it returns the value and does not set it to undefined
                if (arguments.length < 2 || value === undefined) {
                    result = this.data(prop);
                } else {
                    result = this.data(prop, value);
                }
            }
            return result;
        };

        $.jqmData = function (elem, prop, value) {
            var result;
            if (typeof prop !== "undefined") {
                result = $.data(elem, prop ? $.mobile.nsNormalize(prop) : prop, value);
            }
            return result;
        };

        $.fn.jqmRemoveData = function (prop) {
            return this.removeData($.mobile.nsNormalize(prop));
        };

        $.jqmRemoveData = function (elem, prop) {
            return $.removeData(elem, $.mobile.nsNormalize(prop));
        };

        $.find = function (selector, context, ret, extra) {
            if (selector.indexOf(":jqmData") > -1) {
                selector = selector.replace(jqmDataRE, "[data-" + ($.mobile.ns || "") + "$1]");
            }

            return oldFind.call(this, selector, context, ret, extra);
        };

        $.extend($.find, oldFind);

        return $.mobile;
    });

    /*!
     * jQuery Mobile Core @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>group: exclude

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('core', [
                "./defaults",
                "./data",
                "./helpers"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function () {});

    /*!
     * jQuery Mobile Widget @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Widget Factory
//>>group: Core
//>>description: Widget factory extentions for mobile.
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('widget', [
                "jquery",
                "./ns",
                "jquery-ui/widget",
                "./data"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        return $.mobile.widget = $.mobile.widget || {};

    });

    /*!
     * jQuery Mobile Theme Option @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Widget Theme
//>>group: Widgets
//>>description: Adds Theme option to widgets
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('widgets/widget.theme', [
                "jquery",
                "../core",
                "../widget"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        $.mobile.widget.theme = {
            _create: function () {
                var that = this;
                this._super();
                $.each(this._themeElements(), function (i, toTheme) {
                    that._addClass(
                            toTheme.element,
                            null,
                            toTheme.prefix + that.options[ toTheme.option || "theme" ]
                            );
                });
            },

            _setOption: function (key, value) {
                var that = this;
                $.each(this._themeElements(), function (i, toTheme) {
                    var themeOption = (toTheme.option || "theme");

                    if (themeOption === key) {
                        that._removeClass(
                                toTheme.element,
                                null,
                                toTheme.prefix + that.options[ toTheme.option || "theme" ]
                                )
                                ._addClass(toTheme.element, null, toTheme.prefix + value);
                    }
                });
                this._superApply(arguments);
            }
        };

        return $.mobile.widget.theme;

    });

    /*!
     * jQuery Mobile Loader @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Loading Message
//>>group: Widgets
//>>description: Loading message for page transitions
//>>docs: http://api.jquerymobile.com/loader/
//>>demos: http://demos.jquerymobile.com/@VERSION/loader/
//>>css.structure: ../css/structure/jquery.mobile.core.css
//>>css.theme: ../css/themes/default/jquery.mobile.theme.css

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('widgets/loader', [
                "jquery",
                "../helpers",
                "../defaults",
                "./widget.theme",
                "../widget"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        var html = $("html");

        $.widget("mobile.loader", {
            version: "@VERSION",

            // NOTE if the global config settings are defined they will override these
            //      options
            options: {
                classes: {
                    "ui-loader": "ui-corner-all",
                    "ui-loader-icon": "ui-icon-loading"
                },

                enhanced: false,

                // The theme for the loading message
                theme: "a",

                // Whether the text in the loading message is shown
                textVisible: false,

                // The text to be displayed when the popup is shown
                text: "loading"
            },

            _create: function () {
                this.loader = {};

                if (this.options.enhanced) {
                    this.loader.span = this.element.children("span");
                    this.loader.header = this.element.children("h1");
                } else {
                    this.loader.span = $("<span>");
                    this.loader.header = $("<h1>");
                }

                this._addClass("ui-loader");
                this._addClass(this.loader.span, "ui-loader-icon");
                this._addClass(this.loader.header, "ui-loader-header");

                if (!this.options.enhanced) {
                    this.element
                            .append(this.loader.span)
                            .append(this.loader.header);
                }
            },

            _themeElements: function () {
                return [{
                        element: this.element,
                        prefix: "ui-body-"
                    }];
            },

            // Turn on/off page loading message. Theme doubles as an object argument with the following
            // shape: { theme: '', text: '', html: '', textVisible: '' }
            // NOTE that the $.mobile.loading* settings and params past the first are deprecated
            // TODO sweet jesus we need to break some of this out
            show: function (theme, msgText, textonly) {
                var textVisible, message, loadSettings, currentTheme;

                // Use the prototype options so that people can set them globally at mobile init.
                // Consistency, it's what's for dinner.
                if ($.type(theme) === "object") {
                    loadSettings = $.extend({}, this.options, theme);

                    theme = loadSettings.theme;
                } else {
                    loadSettings = this.options;

                    // Here we prefer the theme value passed as a string argument, then we prefer the
                    // global option because we can't use undefined default prototype options, then the
                    // prototype option
                    theme = theme || loadSettings.theme;
                }

                // Set the message text, prefer the param, then the settings object then loading message
                message = msgText || (loadSettings.text === false ? "" : loadSettings.text);

                // Prepare the DOM
                this._addClass(html, "ui-loading");

                textVisible = loadSettings.textVisible;

                currentTheme = this.element.attr("class").match(/\bui-body-[a-z]\b/) || [];

                // Add the proper css given the options (theme, text, etc). Force text visibility if the
                // second argument was supplied, or if the text was explicitly set in the object args.
                this._removeClass.apply(this,
                        ["ui-loader-verbose ui-loader-default ui-loader-textonly"]
                        .concat(currentTheme))
                        ._addClass("ui-loader-" +
                                (textVisible || msgText || theme.text ? "verbose" : "default") +
                                (loadSettings.textonly || textonly ? " ui-loader-textonly" : ""),
                                "ui-body-" + theme);

                this.loader.header.text(message);

                // If the pagecontainer widget has been defined we may use the :mobile-pagecontainer and
                // attach to the element on which the pagecontainer widget has been defined. If not, we
                // attach to the body.
                // TODO: Replace the selector below with $.mobile.pagecontainers[] once #7947 lands
                this.element.appendTo($.mobile.pagecontainer ?
                        $(":mobile-pagecontainer") : $("body"));
            },

            hide: function () {
                this._removeClass(html, "ui-loading");
            }
        });

        return $.widget("mobile.loader", $.mobile.loader, $.mobile.widget.theme);

    });

    /*!
     * jQuery Mobile Loader Backcompat @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Loading Message Backcompat
//>>group: Widgets
//>>description: The backwards compatible portions of the loader widget

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('widgets/loader.backcompat', [
                "jquery",
                "./loader"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        if ($.mobileBackcompat !== false) {
            $.widget("mobile.loader", $.mobile.loader, {
                options: {

                    // Custom html for the inner content of the loading message
                    html: ""
                },

                // DEPRECATED as of 1.5.0 and will be removed in 1.6.0 - we no longer support browsers
                // incapable of native fixed support
                fakeFixLoader: $.noop,

                // DEPRECATED as of 1.5.0 and will be removed in 1.6.0 - we no longer support browsers
                // incapable of native fixed support
                checkLoaderPosition: $.noop,

                show: function (theme) {
                    var html;

                    this.resetHtml();

                    this._superApply(arguments);

                    html = ($.type(theme) === "object" && theme.html || this.options.html);

                    if (html) {
                        this.element.html(html);
                    }
                },

                resetHtml: function () {
                    this.element
                            .empty()
                            .append(this.loader.span)
                            .append(this.loader.header.empty());
                }
            });
        }

        return $.mobile.loader;

    });

    /*!
     * jQuery Mobile Match Media Polyfill @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Match Media Polyfill
//>>group: Utilities
//>>description: A workaround for browsers without window.matchMedia

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('media', [
                "jquery",
                "./core"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        /*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
        window.matchMedia = window.matchMedia || (function (doc, undefined) {

            var bool,
                    docElem = doc.documentElement,
                    refNode = docElem.firstElementChild || docElem.firstChild,
                    // fakeBody required for <FF4 when executed in <head>
                    fakeBody = doc.createElement("body"),
                    div = doc.createElement("div");

            div.id = "mq-test-1";
            div.style.cssText = "position:absolute;top:-100em";
            fakeBody.style.background = "none";
            fakeBody.appendChild(div);

            return function (q) {

                div.innerHTML = "&shy;<style media=\"" + q + "\"> #mq-test-1 { width: 42px; }</style>";

                docElem.insertBefore(fakeBody, refNode);
                bool = div.offsetWidth === 42;
                docElem.removeChild(fakeBody);

                return {
                    matches: bool,
                    media: q
                };

            };

        }(document));

// $.mobile.media uses matchMedia to return a boolean.
        $.mobile.media = function (q) {
            var mediaQueryList = window.matchMedia(q);
            // Firefox returns null in a hidden iframe
            return mediaQueryList && mediaQueryList.matches;
        };

        return $.mobile.media;

    });

    /*!
     * jQuery Mobile Touch Support Test @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Touch support test
//>>group: Core
//>>description: Touch feature test

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('support/touch', [
                "jquery",
                "../ns"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        var support = {
            touch: "ontouchend" in document
        };

        $.mobile.support = $.mobile.support || {};
        $.extend($.support, support);
        $.extend($.mobile.support, support);

        return $.support;
    });

    /*!
     * jQuery Mobile Orientation @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Orientation support test
//>>group: Core
//>>description: Feature test for orientation

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('support/orientation', ["jquery"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        $.extend($.support, {
            orientation: "orientation" in window && "onorientationchange" in window
        });

        return $.support;
    });


    /*!
     * jQuery Mobile Support Tests @VERSION
     * http://jquerymobile.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>description: Assorted tests to qualify browsers by detecting features
//>>label: Support Tests
//>>group: Core

    (function (factory) {
        if (typeof define === "function" && define.amd) {

            // AMD. Register as an anonymous module.
            define('support', [
                "jquery",
                "./core",
                "./media",
                "./support/touch",
                "./support/orientation"], factory);
        } else {

            // Browser globals
            factory(jQuery);
        }
    })(function ($) {

        var fakeBody = $("<body>").prependTo("html"),
                fbCSS = fakeBody[ 0 ].style,
                vendors = ["Webkit", "Moz", "O"],
                webos = "palmGetResource" in window, //only used to rule out scrollTop
                operamini = window.operamini && ({}).toString.call(window.operamini) === "[object OperaMini]",
                nokiaLTE7_3;

// thx Modernizr
        function propExists(prop) {
            var uc_prop = prop.charAt(0).toUpperCase() + prop.substr(1),
                    props = (prop + " " + vendors.join(uc_prop + " ") + uc_prop).split(" "),
                    v;

            for (v in props) {
                if (fbCSS[ props[ v ] ] !== undefined) {
                    return true;
                }
            }
        }
        var bb = window.blackberry && !propExists("-webkit-transform"); //only used to rule out box shadow, as it's filled opaque on BB 5 and lower

// inline SVG support test
        function inlineSVG() {
            // Thanks Modernizr & Erik Dahlstrom
            var w = window,
                    svg = !!w.document.createElementNS && !!w.document.createElementNS("http://www.w3.org/2000/svg", "svg").createSVGRect && !(w.opera && navigator.userAgent.indexOf("Chrome") === -1),
                    support = function (data) {
                        if (!(data && svg)) {
                            $("html").addClass("ui-nosvg");
                        }
                    },
                    img = new w.Image();

            img.onerror = function () {
                support(false);
            };
            img.onload = function () {
                support(img.width === 1 && img.height === 1);
            };
            img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        }

        function transform3dTest() {
            var mqProp = "transform-3d",
                    // Because the `translate3d` test below throws false positives in Android:
                    ret = $.mobile.media("(-" + vendors.join("-" + mqProp + "),(-") + "-" + mqProp + "),(" + mqProp + ")"),
                    el, transforms, t;

            if (ret) {
                return !!ret;
            }

            el = document.createElement("div");
            transforms = {
                // We’re omitting Opera for the time being; MS uses unprefixed.
                "MozTransform": "-moz-transform",
                "transform": "transform"
            };

            fakeBody.append(el);

            for (t in transforms) {
                if (el.style[ t ] !== undefined) {
                    el.style[ t ] = "translate3d( 100px, 1px, 1px )";
                    ret = window.getComputedStyle(el).getPropertyValue(transforms[ t ]);
                }
            }
            return (!!ret && ret !== "none");
        }

// Thanks Modernizr
        function cssPointerEventsTest() {
            var element = document.createElement("x"),
                    documentElement = document.documentElement,
                    getComputedStyle = window.getComputedStyle,
                    supports;

            if (!("pointerEvents" in element.style)) {
                return false;
            }

            element.style.pointerEvents = "auto";
            element.style.pointerEvents = "x";
            documentElement.appendChild(element);
            supports = getComputedStyle &&
                    getComputedStyle(element, "").pointerEvents === "auto";
            documentElement.removeChild(element);
            return !!supports;
        }

        function boundingRect() {
            var div = document.createElement("div");
            return typeof div.getBoundingClientRect !== "undefined";
        }

// non-UA-based IE version check by James Padolsey, modified by jdalton - from http://gist.github.com/527683
// allows for inclusion of IE 6+, including Windows Mobile 7
        $.extend($.mobile, {browser: {}});
        $.mobile.browser.oldIE = (function () {
            var v = 3,
                    div = document.createElement("div"),
                    a = div.all || [];

            do {
                div.innerHTML = "<!--[if gt IE " + (++v) + "]><br><![endif]-->";
            } while (a[ 0 ]);

            return v > 4 ? v : !v;
        })();
        $.mobile.browser.newIEMobile = (function () {
            var div = document.createElement("div");
            return ((!$.mobile.browser.oldIE) &&
                    "onmsgesturehold" in div &&
                    "ontouchstart" in div &&
                    "onpointerdown" in div);
        })();

        function fixedPosition() {
            var w = window,
                    ua = navigator.userAgent,
                    platform = navigator.platform,
                    // Rendering engine is Webkit, and capture major version
                    wkmatch = ua.match(/AppleWebKit\/([0-9]+)/),
                    wkversion = !!wkmatch && wkmatch[ 1 ],
                    ffmatch = ua.match(/Fennec\/([0-9]+)/),
                    ffversion = !!ffmatch && ffmatch[ 1 ],
                    operammobilematch = ua.match(/Opera Mobi\/([0-9]+)/),
                    omversion = !!operammobilematch && operammobilematch[ 1 ];

            if (
                    // iOS 4.3 and older : Platform is iPhone/Pad/Touch and Webkit version is less than 534 (ios5)
                            ((platform.indexOf("iPhone") > -1 || platform.indexOf("iPad") > -1 || platform.indexOf("iPod") > -1) && wkversion && wkversion < 534) ||
                            // Opera Mini
                                    (w.operamini && ({}).toString.call(w.operamini) === "[object OperaMini]") ||
                                    (operammobilematch && omversion < 7458) ||
                                    //Android lte 2.1: Platform is Android and Webkit version is less than 533 (Android 2.2)
                                            (ua.indexOf("Android") > -1 && wkversion && wkversion < 533) ||
                                            // Firefox Mobile before 6.0 -
                                                    (ffversion && ffversion < 6) ||
                                                    // WebOS less than 3
                                                            ("palmGetResource" in window && wkversion && wkversion < 534) ||
                                                            // MeeGo
                                                                    (ua.indexOf("MeeGo") > -1 && ua.indexOf("NokiaBrowser/8.5.0") > -1)) {
                                                        return false;
                                                    }

                                                    return true;
                                                }

                                                $.extend($.support, {
                                                    // Note, Chrome for iOS has an extremely quirky implementation of popstate.
                                                    // We've chosen to take the shortest path to a bug fix here for issue #5426
                                                    // See the following link for information about the regex chosen
                                                    // https://developers.google.com/chrome/mobile/docs/user-agent#chrome_for_ios_user-agent
                                                    pushState: "pushState" in history &&
                                                            "replaceState" in history &&
                                                            // When running inside a FF iframe, calling replaceState causes an error
                                                            !(window.navigator.userAgent.indexOf("Firefox") >= 0 && window.top !== window) &&
                                                            (window.navigator.userAgent.search(/CriOS/) === -1),

                                                    mediaquery: $.mobile.media("only all"),
                                                    cssPseudoElement: !!propExists("content"),
                                                    touchOverflow: !!propExists("overflowScrolling"),
                                                    cssTransform3d: transform3dTest(),
                                                    boxShadow: !!propExists("boxShadow") && !bb,
                                                    fixedPosition: fixedPosition(),
                                                    scrollTop: ("pageXOffset" in window ||
                                                            "scrollTop" in document.documentElement ||
                                                            "scrollTop" in fakeBody[ 0 ]) && !webos && !operamini,

                                                    cssPointerEvents: cssPointerEventsTest(),
                                                    boundingRect: boundingRect(),
                                                    inlineSVG: inlineSVG
                                                });

                                                fakeBody.remove();

// $.mobile.ajaxBlacklist is used to override ajaxEnabled on platforms that have known conflicts with hash history updates (BB5, Symbian)
// or that generally work better browsing in regular http for full page refreshes (Opera Mini)
// Note: This detection below is used as a last resort.
// We recommend only using these detection methods when all other more reliable/forward-looking approaches are not possible
                                                nokiaLTE7_3 = (function () {

                                                    var ua = window.navigator.userAgent;

                                                    //The following is an attempt to match Nokia browsers that are running Symbian/s60, with webkit, version 7.3 or older
                                                    return ua.indexOf("Nokia") > -1 &&
                                                            (ua.indexOf("Symbian/3") > -1 || ua.indexOf("Series60/5") > -1) &&
                                                            ua.indexOf("AppleWebKit") > -1 &&
                                                            ua.match(/(BrowserNG|NokiaBrowser)\/7\.[0-3]/);
                                                })();

// Support conditions that must be met in order to proceed
// default enhanced qualifications are media query support OR IE 7+

                                                $.mobile.gradeA = function () {
                                                    return (($.support.mediaquery && $.support.cssPseudoElement) || $.mobile.browser.oldIE && $.mobile.browser.oldIE >= 8) && ($.support.boundingRect || $.fn.jquery.match(/1\.[0-7+]\.[0-9+]?/) !== null);
                                                };

                                                $.mobile.ajaxBlacklist =
                                                        // BlackBerry browsers, pre-webkit
                                                        window.blackberry && !window.WebKitPoint ||
                                                        // Opera Mini
                                                        operamini ||
                                                        // Symbian webkits pre 7.3
                                                        nokiaLTE7_3;

// Lastly, this workaround is the only way we've found so far to get pre 7.3 Symbian webkit devices
// to render the stylesheets when they're referenced before this script, as we'd recommend doing.
// This simply reappends the CSS in place, which for some reason makes it apply
                                                if (nokiaLTE7_3) {
                                                    $(function () {
                                                        $("head link[rel='stylesheet']").attr("rel", "alternate stylesheet").attr("rel", "stylesheet");
                                                    });
                                                }

// For ruling out shadows via css
                                                if (!$.support.boxShadow) {
                                                    $("html").addClass("ui-noboxshadow");
                                                }

                                                return $.support;

                                            });

                                    /*!
                                     * jQuery Mobile Navigate Event @VERSION
                                     * http://jquerymobile.com
                                     *
                                     * Copyright jQuery Foundation and other contributors
                                     * Released under the MIT license.
                                     * http://jquery.org/license
                                     */

//>>label: Navigate
//>>group: Events
//>>description: Provides a wrapper around hashchange and popstate
//>>docs: http://api.jquerymobile.com/navigate/
//>>demos: http://api.jquerymobile.com/@VERSION/navigation/

// TODO break out pushstate support test so we don't depend on the whole thing
                                    (function (factory) {
                                        if (typeof define === "function" && define.amd) {

                                            // AMD. Register as an anonymous module.
                                            define('events/navigate', [
                                                "jquery",
                                                "./../ns",
                                                "./../support"], factory);
                                        } else {

                                            // Browser globals
                                            factory(jQuery);
                                        }
                                    })(function ($) {

                                        var $win = $.mobile.window, self,
                                                dummyFnToInitNavigate = function () {};

                                        $.event.special.beforenavigate = {
                                            setup: function () {
                                                $win.on("navigate", dummyFnToInitNavigate);
                                            },

                                            teardown: function () {
                                                $win.off("navigate", dummyFnToInitNavigate);
                                            }
                                        };

                                        $.event.special.navigate = self = {
                                            bound: false,

                                            pushStateEnabled: true,

                                            originalEventName: undefined,

                                            // If pushstate support is present and push state support is defined to
                                            // be true on the mobile namespace.
                                            isPushStateEnabled: function () {
                                                return $.support.pushState &&
                                                        $.mobile.pushStateEnabled === true &&
                                                        this.isHashChangeEnabled();
                                            },

                                            // !! assumes mobile namespace is present
                                            isHashChangeEnabled: function () {
                                                return $.mobile.hashListeningEnabled === true;
                                            },

                                            // TODO a lot of duplication between popstate and hashchange
                                            popstate: function (event) {
                                                var newEvent, beforeNavigate, state;

                                                if (event.isDefaultPrevented()) {
                                                    return;
                                                }

                                                newEvent = new $.Event("navigate");
                                                beforeNavigate = new $.Event("beforenavigate");
                                                state = event.originalEvent.state || {};

                                                beforeNavigate.originalEvent = event;
                                                $win.trigger(beforeNavigate);

                                                if (beforeNavigate.isDefaultPrevented()) {
                                                    return;
                                                }

                                                if (event.historyState) {
                                                    $.extend(state, event.historyState);
                                                }

                                                // Make sure the original event is tracked for the end
                                                // user to inspect incase they want to do something special
                                                newEvent.originalEvent = event;

                                                // NOTE we let the current stack unwind because any assignment to
                                                //      location.hash will stop the world and run this event handler. By
                                                //      doing this we create a similar behavior to hashchange on hash
                                                //      assignment
                                                setTimeout(function () {
                                                    $win.trigger(newEvent, {
                                                        state: state
                                                    });
                                                }, 0);
                                            },

                                            hashchange: function (event /*, data */) {
                                                var newEvent = new $.Event("navigate"),
                                                        beforeNavigate = new $.Event("beforenavigate");

                                                beforeNavigate.originalEvent = event;
                                                $win.trigger(beforeNavigate);

                                                if (beforeNavigate.isDefaultPrevented()) {
                                                    return;
                                                }

                                                // Make sure the original event is tracked for the end
                                                // user to inspect incase they want to do something special
                                                newEvent.originalEvent = event;

                                                // Trigger the hashchange with state provided by the user
                                                // that altered the hash
                                                $win.trigger(newEvent, {
                                                    // Users that want to fully normalize the two events
                                                    // will need to do history management down the stack and
                                                    // add the state to the event before this binding is fired
                                                    // TODO consider allowing for the explicit addition of callbacks
                                                    //      to be fired before this value is set to avoid event timing issues
                                                    state: event.hashchangeState || {}
                                                });
                                            },

                                            // TODO We really only want to set this up once
                                            //      but I'm not clear if there's a beter way to achieve
                                            //      this with the jQuery special event structure
                                            setup: function ( /* data, namespaces */ ) {
                                                if (self.bound) {
                                                    return;
                                                }

                                                self.bound = true;

                                                if (self.isPushStateEnabled()) {
                                                    self.originalEventName = "popstate";
                                                    $win.bind("popstate.navigate", self.popstate);
                                                } else if (self.isHashChangeEnabled()) {
                                                    self.originalEventName = "hashchange";
                                                    $win.bind("hashchange.navigate", self.hashchange);
                                                }
                                            }
                                        };

                                        return $.event.special.navigate;
                                    });

                                    /*!
                                     * jQuery Mobile Virtual Mouse @VERSION
                                     * http://jquerymobile.com
                                     *
                                     * Copyright jQuery Foundation and other contributors
                                     * Released under the MIT license.
                                     * http://jquery.org/license
                                     */

//>>label: Virtual Mouse (vmouse) Bindings
//>>group: Core
//>>description: Normalizes touch/mouse events.
//>>docs: http://api.jquerymobile.com/?s=vmouse

// This plugin is an experiment for abstracting away the touch and mouse
// events so that developers don't have to worry about which method of input
// the device their document is loaded on supports.
//
// The idea here is to allow the developer to register listeners for the
// basic mouse events, such as mousedown, mousemove, mouseup, and click,
// and the plugin will take care of registering the correct listeners
// behind the scenes to invoke the listener at the fastest possible time
// for that device, while still retaining the order of event firing in
// the traditional mouse environment, should multiple handlers be registered
// on the same element for different events.
//
// The current version exposes the following virtual events to jQuery bind methods:
// "vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel"

                                    (function (factory) {
                                        if (typeof define === "function" && define.amd) {

                                            // AMD. Register as an anonymous module.
                                            define('vmouse', ["jquery"], factory);
                                        } else {

                                            // Browser globals
                                            factory(jQuery);
                                        }
                                    })(function ($) {

                                        var dataPropertyName = "virtualMouseBindings",
                                                touchTargetPropertyName = "virtualTouchID",
                                                touchEventProps = "clientX clientY pageX pageY screenX screenY".split(" "),
                                                virtualEventNames = "vmouseover vmousedown vmousemove vmouseup vclick vmouseout vmousecancel".split(" "),
                                                generalProps = ("altKey bubbles cancelable ctrlKey currentTarget detail eventPhase " +
                                                        "metaKey relatedTarget shiftKey target timeStamp view which").split(" "),
                                                mouseHookProps = $.event.mouseHooks ? $.event.mouseHooks.props : [],
                                                mouseEventProps = generalProps.concat(mouseHookProps),
                                                activeDocHandlers = {},
                                                resetTimerID = 0,
                                                startX = 0,
                                                startY = 0,
                                                didScroll = false,
                                                clickBlockList = [],
                                                blockMouseTriggers = false,
                                                blockTouchTriggers = false,
                                                eventCaptureSupported = "addEventListener" in document,
                                                $document = $(document),
                                                nextTouchID = 1,
                                                lastTouchID = 0, threshold,
                                                i;

                                        $.vmouse = {
                                            moveDistanceThreshold: 10,
                                            clickDistanceThreshold: 10,
                                            resetTimerDuration: 1500,
                                            maximumTimeBetweenTouches: 100
                                        };

                                        function getNativeEvent(event) {

                                            while (event && typeof event.originalEvent !== "undefined") {
                                                event = event.originalEvent;
                                            }
                                            return event;
                                        }

                                        function createVirtualEvent(event, eventType) {

                                            var t = event.type,
                                                    oe, props, ne, prop, ct, touch, i, j, len;

                                            event = $.Event(event);
                                            event.type = eventType;

                                            oe = event.originalEvent;
                                            props = generalProps;

                                            // addresses separation of $.event.props in to $.event.mouseHook.props and Issue 3280
                                            // https://github.com/jquery/jquery-mobile/issues/3280
                                            if (t.search(/^(mouse|click)/) > -1) {
                                                props = mouseEventProps;
                                            }

                                            // copy original event properties over to the new event
                                            // this would happen if we could call $.event.fix instead of $.Event
                                            // but we don't have a way to force an event to be fixed multiple times
                                            if (oe) {
                                                for (i = props.length; i; ) {
                                                    prop = props[ --i ];
                                                    event[ prop ] = oe[ prop ];
                                                }
                                            }

                                            // make sure that if the mouse and click virtual events are generated
                                            // without a .which one is defined
                                            if (t.search(/mouse(down|up)|click/) > -1 && !event.which) {
                                                event.which = 1;
                                            }

                                            if (t.search(/^touch/) !== -1) {
                                                ne = getNativeEvent(oe);
                                                t = ne.touches;
                                                ct = ne.changedTouches;
                                                touch = (t && t.length) ? t[ 0 ] : ((ct && ct.length) ? ct[ 0 ] : undefined);

                                                if (touch) {
                                                    for (j = 0, len = touchEventProps.length; j < len; j++) {
                                                        prop = touchEventProps[ j ];
                                                        event[ prop ] = touch[ prop ];
                                                    }
                                                }
                                            }

                                            return event;
                                        }

                                        function getVirtualBindingFlags(element) {

                                            var flags = {},
                                                    b, k;

                                            while (element) {

                                                b = $.data(element, dataPropertyName);

                                                for (k in b) {
                                                    if (b[ k ]) {
                                                        flags[ k ] = flags.hasVirtualBinding = true;
                                                    }
                                                }
                                                element = element.parentNode;
                                            }
                                            return flags;
                                        }

                                        function getClosestElementWithVirtualBinding(element, eventType) {
                                            var b;
                                            while (element) {

                                                b = $.data(element, dataPropertyName);

                                                if (b && (!eventType || b[ eventType ])) {
                                                    return element;
                                                }
                                                element = element.parentNode;
                                            }
                                            return null;
                                        }

                                        function enableTouchBindings() {
                                            blockTouchTriggers = false;
                                        }

                                        function disableTouchBindings() {
                                            blockTouchTriggers = true;
                                        }

                                        function enableMouseBindings() {
                                            lastTouchID = 0;
                                            clickBlockList.length = 0;
                                            blockMouseTriggers = false;

                                            // When mouse bindings are enabled, our
                                            // touch bindings are disabled.
                                            disableTouchBindings();
                                        }

                                        function disableMouseBindings() {
                                            // When mouse bindings are disabled, our
                                            // touch bindings are enabled.
                                            enableTouchBindings();
                                        }

                                        function clearResetTimer() {
                                            if (resetTimerID) {
                                                clearTimeout(resetTimerID);
                                                resetTimerID = 0;
                                            }
                                        }

                                        function startResetTimer() {
                                            clearResetTimer();
                                            resetTimerID = setTimeout(function () {
                                                resetTimerID = 0;
                                                enableMouseBindings();
                                            }, $.vmouse.resetTimerDuration);
                                        }

                                        function triggerVirtualEvent(eventType, event, flags) {
                                            var ve;

                                            if ((flags && flags[ eventType ]) ||
                                                    (!flags && getClosestElementWithVirtualBinding(event.target, eventType))) {

                                                ve = createVirtualEvent(event, eventType);

                                                $(event.target).trigger(ve);
                                            }

                                            return ve;
                                        }

                                        function mouseEventCallback(event) {
                                            var touchID = $.data(event.target, touchTargetPropertyName),
                                                    ve;

                                            // It is unexpected if a click event is received before a touchend
                                            // or touchmove event, however this is a known behavior in Mobile
                                            // Safari when Mobile VoiceOver (as of iOS 8) is enabled and the user
                                            // double taps to activate a link element. In these cases if a touch
                                            // event is not received within the maximum time between touches,
                                            // re-enable mouse bindings and call the mouse event handler again.
                                            if (event.type === "click" && $.data(event.target, "lastTouchType") === "touchstart") {
                                                setTimeout(function () {
                                                    if ($.data(event.target, "lastTouchType") === "touchstart") {
                                                        enableMouseBindings();
                                                        delete $.data(event.target).lastTouchType;
                                                        mouseEventCallback(event);
                                                    }
                                                }, $.vmouse.maximumTimeBetweenTouches);
                                            }

                                            if (!blockMouseTriggers && (!lastTouchID || lastTouchID !== touchID)) {
                                                ve = triggerVirtualEvent("v" + event.type, event);
                                                if (ve) {
                                                    if (ve.isDefaultPrevented()) {
                                                        event.preventDefault();
                                                    }
                                                    if (ve.isPropagationStopped()) {
                                                        event.stopPropagation();
                                                    }
                                                    if (ve.isImmediatePropagationStopped()) {
                                                        event.stopImmediatePropagation();
                                                    }
                                                }
                                            }
                                        }

                                        function handleTouchStart(event) {

                                            var touches = getNativeEvent(event).touches,
                                                    target, flags, t;

                                            if (touches && touches.length === 1) {

                                                target = event.target;
                                                flags = getVirtualBindingFlags(target);

                                                $.data(event.target, "lastTouchType", event.type);

                                                if (flags.hasVirtualBinding) {

                                                    lastTouchID = nextTouchID++;
                                                    $.data(target, touchTargetPropertyName, lastTouchID);

                                                    clearResetTimer();

                                                    disableMouseBindings();
                                                    didScroll = false;

                                                    t = getNativeEvent(event).touches[ 0 ];
                                                    startX = t.pageX;
                                                    startY = t.pageY;

                                                    triggerVirtualEvent("vmouseover", event, flags);
                                                    triggerVirtualEvent("vmousedown", event, flags);
                                                }
                                            }
                                        }

                                        function handleScroll(event) {
                                            if (blockTouchTriggers) {
                                                return;
                                            }

                                            if (!didScroll) {
                                                triggerVirtualEvent("vmousecancel", event, getVirtualBindingFlags(event.target));
                                            }

                                            $.data(event.target, "lastTouchType", event.type);

                                            didScroll = true;
                                            startResetTimer();
                                        }

                                        function handleTouchMove(event) {
                                            if (blockTouchTriggers) {
                                                return;
                                            }

                                            var t = getNativeEvent(event).touches[ 0 ],
                                                    didCancel = didScroll,
                                                    moveThreshold = $.vmouse.moveDistanceThreshold,
                                                    flags = getVirtualBindingFlags(event.target);

                                            $.data(event.target, "lastTouchType", event.type);

                                            didScroll = didScroll ||
                                                    (Math.abs(t.pageX - startX) > moveThreshold ||
                                                            Math.abs(t.pageY - startY) > moveThreshold);

                                            if (didScroll && !didCancel) {
                                                triggerVirtualEvent("vmousecancel", event, flags);
                                            }

                                            triggerVirtualEvent("vmousemove", event, flags);
                                            startResetTimer();
                                        }

                                        function handleTouchEnd(event) {
                                            if (blockTouchTriggers || $.data(event.target, "lastTouchType") === undefined) {
                                                return;
                                            }

                                            disableTouchBindings();
                                            delete $.data(event.target).lastTouchType;

                                            var flags = getVirtualBindingFlags(event.target),
                                                    ve, t;
                                            triggerVirtualEvent("vmouseup", event, flags);

                                            if (!didScroll) {
                                                ve = triggerVirtualEvent("vclick", event, flags);
                                                if (ve && ve.isDefaultPrevented()) {
                                                    // The target of the mouse events that follow the touchend
                                                    // event don't necessarily match the target used during the
                                                    // touch. This means we need to rely on coordinates for blocking
                                                    // any click that is generated.
                                                    t = getNativeEvent(event).changedTouches[ 0 ];
                                                    clickBlockList.push({
                                                        touchID: lastTouchID,
                                                        x: t.clientX,
                                                        y: t.clientY
                                                    });

                                                    // Prevent any mouse events that follow from triggering
                                                    // virtual event notifications.
                                                    blockMouseTriggers = true;
                                                }
                                            }
                                            triggerVirtualEvent("vmouseout", event, flags);
                                            didScroll = false;

                                            startResetTimer();
                                        }

                                        function hasVirtualBindings(ele) {
                                            var bindings = $.data(ele, dataPropertyName),
                                                    k;

                                            if (bindings) {
                                                for (k in bindings) {
                                                    if (bindings[ k ]) {
                                                        return true;
                                                    }
                                                }
                                            }
                                            return false;
                                        }

                                        function dummyMouseHandler() {
                                        }

                                        function getSpecialEventObject(eventType) {
                                            var realType = eventType.substr(1);

                                            return {
                                                setup: function ( /* data, namespace */ ) {
                                                    // If this is the first virtual mouse binding for this element,
                                                    // add a bindings object to its data.

                                                    if (!hasVirtualBindings(this)) {
                                                        $.data(this, dataPropertyName, {});
                                                    }

                                                    // If setup is called, we know it is the first binding for this
                                                    // eventType, so initialize the count for the eventType to zero.
                                                    var bindings = $.data(this, dataPropertyName);
                                                    bindings[ eventType ] = true;

                                                    // If this is the first virtual mouse event for this type,
                                                    // register a global handler on the document.

                                                    activeDocHandlers[ eventType ] = (activeDocHandlers[ eventType ] || 0) + 1;

                                                    if (activeDocHandlers[ eventType ] === 1) {
                                                        $document.bind(realType, mouseEventCallback);
                                                    }

                                                    // Some browsers, like Opera Mini, won't dispatch mouse/click events
                                                    // for elements unless they actually have handlers registered on them.
                                                    // To get around this, we register dummy handlers on the elements.

                                                    $(this).bind(realType, dummyMouseHandler);

                                                    // For now, if event capture is not supported, we rely on mouse handlers.
                                                    if (eventCaptureSupported) {
                                                        // If this is the first virtual mouse binding for the document,
                                                        // register our touchstart handler on the document.

                                                        activeDocHandlers[ "touchstart" ] = (activeDocHandlers[ "touchstart" ] || 0) + 1;

                                                        if (activeDocHandlers[ "touchstart" ] === 1) {
                                                            $document.bind("touchstart", handleTouchStart)
                                                                    .bind("touchend", handleTouchEnd)

                                                                    // On touch platforms, touching the screen and then dragging your finger
                                                                    // causes the window content to scroll after some distance threshold is
                                                                    // exceeded. On these platforms, a scroll prevents a click event from being
                                                                    // dispatched, and on some platforms, even the touchend is suppressed. To
                                                                    // mimic the suppression of the click event, we need to watch for a scroll
                                                                    // event. Unfortunately, some platforms like iOS don't dispatch scroll
                                                                    // events until *AFTER* the user lifts their finger (touchend). This means
                                                                    // we need to watch both scroll and touchmove events to figure out whether
                                                                    // or not a scroll happenens before the touchend event is fired.

                                                                    .bind("touchmove", handleTouchMove)
                                                                    .bind("scroll", handleScroll);
                                                        }
                                                    }
                                                },

                                                teardown: function ( /* data, namespace */ ) {
                                                    // If this is the last virtual binding for this eventType,
                                                    // remove its global handler from the document.

                                                    --activeDocHandlers[eventType];

                                                    if (!activeDocHandlers[ eventType ]) {
                                                        $document.unbind(realType, mouseEventCallback);
                                                    }

                                                    if (eventCaptureSupported) {
                                                        // If this is the last virtual mouse binding in existence,
                                                        // remove our document touchstart listener.

                                                        --activeDocHandlers["touchstart"];

                                                        if (!activeDocHandlers[ "touchstart" ]) {
                                                            $document.unbind("touchstart", handleTouchStart)
                                                                    .unbind("touchmove", handleTouchMove)
                                                                    .unbind("touchend", handleTouchEnd)
                                                                    .unbind("scroll", handleScroll);
                                                        }
                                                    }

                                                    var $this = $(this),
                                                            bindings = $.data(this, dataPropertyName);

                                                    // teardown may be called when an element was
                                                    // removed from the DOM. If this is the case,
                                                    // jQuery core may have already stripped the element
                                                    // of any data bindings so we need to check it before
                                                    // using it.
                                                    if (bindings) {
                                                        bindings[ eventType ] = false;
                                                    }

                                                    // Unregister the dummy event handler.

                                                    $this.unbind(realType, dummyMouseHandler);

                                                    // If this is the last virtual mouse binding on the
                                                    // element, remove the binding data from the element.

                                                    if (!hasVirtualBindings(this)) {
                                                        $this.removeData(dataPropertyName);
                                                    }
                                                }
                                            };
                                        }

// Expose our custom events to the jQuery bind/unbind mechanism.

                                        for (i = 0; i < virtualEventNames.length; i++) {
                                            $.event.special[ virtualEventNames[ i ] ] = getSpecialEventObject(virtualEventNames[ i ]);
                                        }

// Add a capture click handler to block clicks.
// Note that we require event capture support for this so if the device
// doesn't support it, we punt for now and rely solely on mouse events.
                                        if (eventCaptureSupported) {
                                            document.addEventListener("click", function (e) {
                                                var cnt = clickBlockList.length,
                                                        target = e.target,
                                                        x, y, ele, i, o, touchID;

                                                if (cnt) {
                                                    x = e.clientX;
                                                    y = e.clientY;
                                                    threshold = $.vmouse.clickDistanceThreshold;

                                                    // The idea here is to run through the clickBlockList to see if
                                                    // the current click event is in the proximity of one of our
                                                    // vclick events that had preventDefault() called on it. If we find
                                                    // one, then we block the click.
                                                    //
                                                    // Why do we have to rely on proximity?
                                                    //
                                                    // Because the target of the touch event that triggered the vclick
                                                    // can be different from the target of the click event synthesized
                                                    // by the browser. The target of a mouse/click event that is synthesized
                                                    // from a touch event seems to be implementation specific. For example,
                                                    // some browsers will fire mouse/click events for a link that is near
                                                    // a touch event, even though the target of the touchstart/touchend event
                                                    // says the user touched outside the link. Also, it seems that with most
                                                    // browsers, the target of the mouse/click event is not calculated until the
                                                    // time it is dispatched, so if you replace an element that you touched
                                                    // with another element, the target of the mouse/click will be the new
                                                    // element underneath that point.
                                                    //
                                                    // Aside from proximity, we also check to see if the target and any
                                                    // of its ancestors were the ones that blocked a click. This is necessary
                                                    // because of the strange mouse/click target calculation done in the
                                                    // Android 2.1 browser, where if you click on an element, and there is a
                                                    // mouse/click handler on one of its ancestors, the target will be the
                                                    // innermost child of the touched element, even if that child is no where
                                                    // near the point of touch.

                                                    ele = target;

                                                    while (ele) {
                                                        for (i = 0; i < cnt; i++) {
                                                            o = clickBlockList[ i ];
                                                            touchID = 0;

                                                            if ((ele === target && Math.abs(o.x - x) < threshold && Math.abs(o.y - y) < threshold) ||
                                                                    $.data(ele, touchTargetPropertyName) === o.touchID) {
                                                                // XXX: We may want to consider removing matches from the block list
                                                                //      instead of waiting for the reset timer to fire.
                                                                e.preventDefault();
                                                                e.stopPropagation();
                                                                return;
                                                            }
                                                        }
                                                        ele = ele.parentNode;
                                                    }
                                                }
                                            }, true);
                                        }
                                    });

                                    /*!
                                     * jQuery Mobile Touch Events @VERSION
                                     * http://jquerymobile.com
                                     *
                                     * Copyright jQuery Foundation and other contributors
                                     * Released under the MIT license.
                                     * http://jquery.org/license
                                     */

//>>label: Touch
//>>group: Events
//>>description: Touch events including: touchstart, touchmove, touchend, tap, taphold, swipe, swipeleft, swiperight

                                    (function (factory) {
                                        if (typeof define === "function" && define.amd) {

                                            // AMD. Register as an anonymous module.
                                            define('events/touch', [
                                                "jquery",
                                                "../vmouse",
                                                "../support/touch"], factory);
                                        } else {

                                            // Browser globals
                                            factory(jQuery);
                                        }
                                    })(function ($) {
                                        var $document = $(document),
                                                supportTouch = $.mobile.support.touch,
                                                touchStartEvent = supportTouch ? "touchstart" : "mousedown",
                                                touchStopEvent = supportTouch ? "touchend" : "mouseup",
                                                touchMoveEvent = supportTouch ? "touchmove" : "mousemove";

// setup new event shortcuts
                                        $.each(("touchstart touchmove touchend " +
                                                "tap taphold " +
                                                "swipe swipeleft swiperight").split(" "), function (i, name) {

                                            $.fn[ name ] = function (fn) {
                                                return fn ? this.bind(name, fn) : this.trigger(name);
                                            };

                                            // jQuery < 1.8
                                            if ($.attrFn) {
                                                $.attrFn[ name ] = true;
                                            }
                                        });

                                        function triggerCustomEvent(obj, eventType, event, bubble) {
                                            var originalType = event.type;
                                            event.type = eventType;
                                            if (bubble) {
                                                $.event.trigger(event, undefined, obj);
                                            } else {
                                                $.event.dispatch.call(obj, event);
                                            }
                                            event.type = originalType;
                                        }

// also handles taphold
                                        $.event.special.tap = {
                                            tapholdThreshold: 750,
                                            emitTapOnTaphold: true,
                                            setup: function () {
                                                var thisObject = this,
                                                        $this = $(thisObject),
                                                        isTaphold = false;

                                                $this.bind("vmousedown", function (event) {
                                                    isTaphold = false;
                                                    if (event.which && event.which !== 1) {
                                                        return true;
                                                    }

                                                    var origTarget = event.target,
                                                            timer, clickHandler;

                                                    function clearTapTimer() {
                                                        if (timer) {
                                                            $this.bind("vclick", clickHandler);
                                                            clearTimeout(timer);
                                                        }
                                                    }

                                                    function clearTapHandlers() {
                                                        clearTapTimer();

                                                        $this.unbind("vclick", clickHandler)
                                                                .unbind("vmouseup", clearTapTimer);
                                                        $document.unbind("vmousecancel", clearTapHandlers);
                                                    }

                                                    clickHandler = function (event) {
                                                        clearTapHandlers();

                                                        // ONLY trigger a 'tap' event if the start target is
                                                        // the same as the stop target.
                                                        if (!isTaphold && origTarget === event.target) {
                                                            triggerCustomEvent(thisObject, "tap", event);
                                                        } else if (isTaphold) {
                                                            event.preventDefault();
                                                        }
                                                    };

                                                    $this.bind("vmouseup", clearTapTimer);

                                                    $document.bind("vmousecancel", clearTapHandlers);

                                                    timer = setTimeout(function () {
                                                        if (!$.event.special.tap.emitTapOnTaphold) {
                                                            isTaphold = true;
                                                        }
                                                        timer = 0;
                                                        triggerCustomEvent(thisObject, "taphold", $.Event("taphold", {target: origTarget}));
                                                    }, $.event.special.tap.tapholdThreshold);
                                                });
                                            },
                                            teardown: function () {
                                                $(this).unbind("vmousedown").unbind("vclick").unbind("vmouseup");
                                                $document.unbind("vmousecancel");
                                            }
                                        };

// Also handles swipeleft, swiperight
                                        $.event.special.swipe = {

                                            // More than this horizontal displacement, and we will suppress scrolling.
                                            scrollSupressionThreshold: 30,

                                            // More time than this, and it isn't a swipe.
                                            durationThreshold: 1000,

                                            // Swipe horizontal displacement must be more than this.
                                            horizontalDistanceThreshold: window.devicePixelRatio >= 2 ? 15 : 30,

                                            // Swipe vertical displacement must be less than this.
                                            verticalDistanceThreshold: window.devicePixelRatio >= 2 ? 15 : 30,

                                            getLocation: function (event) {
                                                var winPageX = window.pageXOffset,
                                                        winPageY = window.pageYOffset,
                                                        x = event.clientX,
                                                        y = event.clientY;

                                                if (event.pageY === 0 && Math.floor(y) > Math.floor(event.pageY) ||
                                                        event.pageX === 0 && Math.floor(x) > Math.floor(event.pageX)) {

                                                    // iOS4 clientX/clientY have the value that should have been
                                                    // in pageX/pageY. While pageX/page/ have the value 0
                                                    x = x - winPageX;
                                                    y = y - winPageY;
                                                } else if (y < (event.pageY - winPageY) || x < (event.pageX - winPageX)) {

                                                    // Some Android browsers have totally bogus values for clientX/Y
                                                    // when scrolling/zooming a page. Detectable since clientX/clientY
                                                    // should never be smaller than pageX/pageY minus page scroll
                                                    x = event.pageX - winPageX;
                                                    y = event.pageY - winPageY;
                                                }

                                                return {
                                                    x: x,
                                                    y: y
                                                };
                                            },

                                            start: function (event) {
                                                var data = event.originalEvent.touches ?
                                                        event.originalEvent.touches[ 0 ] : event,
                                                        location = $.event.special.swipe.getLocation(data);
                                                return {
                                                    time: (new Date()).getTime(),
                                                    coords: [location.x, location.y],
                                                    origin: $(event.target)
                                                };
                                            },

                                            stop: function (event) {
                                                var data = event.originalEvent.touches ?
                                                        event.originalEvent.touches[ 0 ] : event,
                                                        location = $.event.special.swipe.getLocation(data);
                                                return {
                                                    time: (new Date()).getTime(),
                                                    coords: [location.x, location.y]
                                                };
                                            },

                                            handleSwipe: function (start, stop, thisObject, origTarget) {
                                                if (stop.time - start.time < $.event.special.swipe.durationThreshold &&
                                                        Math.abs(start.coords[ 0 ] - stop.coords[ 0 ]) > $.event.special.swipe.horizontalDistanceThreshold &&
                                                        Math.abs(start.coords[ 1 ] - stop.coords[ 1 ]) < $.event.special.swipe.verticalDistanceThreshold) {
                                                    var direction = start.coords[ 0 ] > stop.coords[ 0 ] ? "swipeleft" : "swiperight";

                                                    triggerCustomEvent(thisObject, "swipe", $.Event("swipe", {target: origTarget, swipestart: start, swipestop: stop}), true);
                                                    triggerCustomEvent(thisObject, direction, $.Event(direction, {target: origTarget, swipestart: start, swipestop: stop}), true);
                                                    return true;
                                                }
                                                return false;

                                            },

                                            // This serves as a flag to ensure that at most one swipe event event is
                                            // in work at any given time
                                            eventInProgress: false,

                                            setup: function () {
                                                var events,
                                                        thisObject = this,
                                                        $this = $(thisObject),
                                                        context = {};

                                                // Retrieve the events data for this element and add the swipe context
                                                events = $.data(this, "mobile-events");
                                                if (!events) {
                                                    events = {length: 0};
                                                    $.data(this, "mobile-events", events);
                                                }
                                                events.length++;
                                                events.swipe = context;

                                                context.start = function (event) {

                                                    // Bail if we're already working on a swipe event
                                                    if ($.event.special.swipe.eventInProgress) {
                                                        return;
                                                    }
                                                    $.event.special.swipe.eventInProgress = true;

                                                    var stop,
                                                            start = $.event.special.swipe.start(event),
                                                            origTarget = event.target,
                                                            emitted = false;

                                                    context.move = function (event) {
                                                        if (!start || event.isDefaultPrevented()) {
                                                            return;
                                                        }

                                                        stop = $.event.special.swipe.stop(event);
                                                        if (!emitted) {
                                                            emitted = $.event.special.swipe.handleSwipe(start, stop, thisObject, origTarget);
                                                            if (emitted) {

                                                                // Reset the context to make way for the next swipe event
                                                                $.event.special.swipe.eventInProgress = false;
                                                            }
                                                        }
                                                        // prevent scrolling
                                                        if (Math.abs(start.coords[ 0 ] - stop.coords[ 0 ]) > $.event.special.swipe.scrollSupressionThreshold) {
                                                            event.preventDefault();
                                                        }
                                                    };

                                                    context.stop = function () {
                                                        emitted = true;

                                                        // Reset the context to make way for the next swipe event
                                                        $.event.special.swipe.eventInProgress = false;
                                                        $document.off(touchMoveEvent, context.move);
                                                        context.move = null;
                                                    };

                                                    $document.on(touchMoveEvent, context.move)
                                                            .one(touchStopEvent, context.stop);
                                                };
                                                $this.on(touchStartEvent, context.start);
                                            },

                                            teardown: function () {
                                                var events, context;

                                                events = $.data(this, "mobile-events");
                                                if (events) {
                                                    context = events.swipe;
                                                    delete events.swipe;
                                                    events.length--;
                                                    if (events.length === 0) {
                                                        $.removeData(this, "mobile-events");
                                                    }
                                                }

                                                if (context) {
                                                    if (context.start) {
                                                        $(this).off(touchStartEvent, context.start);
                                                    }
                                                    if (context.move) {
                                                        $document.off(touchMoveEvent, context.move);
                                                    }
                                                    if (context.stop) {
                                                        $document.off(touchStopEvent, context.stop);
                                                    }
                                                }
                                            }
                                        };
                                        $.each({
                                            taphold: "tap",
                                            swipeleft: "swipe.left",
                                            swiperight: "swipe.right"
                                        }, function (event, sourceEvent) {

                                            $.event.special[ event ] = {
                                                setup: function () {
                                                    $(this).bind(sourceEvent, $.noop);
                                                },
                                                teardown: function () {
                                                    $(this).unbind(sourceEvent);
                                                }
                                            };
                                        });

                                        return $.event.special;
                                    });


                                    /*!
                                     * jQuery Mobile Scroll Events @VERSION
                                     * http://jquerymobile.com
                                     *
                                     * Copyright jQuery Foundation and other contributors
                                     * Released under the MIT license.
                                     * http://jquery.org/license
                                     */




//>>label: iOS Orientation Change Fix
//>>group: Utilities
//>>description: Fixes the orientation change bug in iOS when switching between landscape and portrait

                                                            (function (factory) {
                                                                if (typeof define === "function" && define.amd) {

                                                                    // AMD. Register as an anonymous module.
                                                                    define('zoom/iosorientationfix', [
                                                                        "jquery",
                                                                        "../core",
                                                                        "../zoom"], factory);
                                                                } else {

                                                                    // Browser globals
                                                                    factory(jQuery);
                                                                }
                                                            })(function ($) {

                                                                $.mobile.iosorientationfixEnabled = true;

// This fix addresses an iOS bug, so return early if the UA claims it's something else.
                                                                var ua = navigator.userAgent,
                                                                        zoom,
                                                                        evt, x, y, z, aig;
                                                                if (!(/iPhone|iPad|iPod/.test(navigator.platform) && /OS [1-5]_[0-9_]* like Mac OS X/i.test(ua) && ua.indexOf("AppleWebKit") > -1)) {
                                                                    $.mobile.iosorientationfixEnabled = false;
                                                                    return;
                                                                }

                                                                zoom = $.mobile.zoom;

                                                                function checkTilt(e) {
                                                                    evt = e.originalEvent;
                                                                    aig = evt.accelerationIncludingGravity;

                                                                    x = Math.abs(aig.x);
                                                                    y = Math.abs(aig.y);
                                                                    z = Math.abs(aig.z);

                                                                    // If portrait orientation and in one of the danger zones
                                                                    if (!window.orientation && (x > 7 || ((z > 6 && y < 8 || z < 8 && y > 6) && x > 5))) {
                                                                        if (zoom.enabled) {
                                                                            zoom.disable();
                                                                        }
                                                                    } else if (!zoom.enabled) {
                                                                        zoom.enable();
                                                                    }
                                                                }

                                                                $.mobile.document.on("mobileinit", function () {
                                                                    if ($.mobile.iosorientationfixEnabled) {
                                                                        $.mobile.window
                                                                                .bind("orientationchange.iosorientationfix", zoom.enable)
                                                                                .bind("devicemotion.iosorientationfix", checkTilt);
                                                                    }
                                                                });

                                                            });

                                                            /*!
                                                             * jQuery Mobile Init @VERSION
                                                             * http://jquerymobile.com
                                                             *
                                                             * Copyright jQuery Foundation and other contributors
                                                             * Released under the MIT license.
                                                             * http://jquery.org/license
                                                             */

//>>label: Init
//>>group: Core
//>>description: Global initialization of the library.

                                                            (function (factory) {
                                                                if (typeof define === "function" && define.amd) {

                                                                    // AMD. Register as an anonymous module.
                                                                    define('init', [
                                                                        "jquery",
                                                                        "./defaults",
                                                                        "./helpers",
                                                                        "./data",
                                                                        "./support",
                                                                        "./widgets/enhancer",
                                                                        "./events/navigate",
                                                                        "./navigation/path",
                                                                        "./navigation/method",
                                                                        "./navigation",
                                                                        "./widgets/loader",
                                                                        "./vmouse"], factory);
                                                                } else {

                                                                    // Browser globals
                                                                    factory(jQuery);
                                                                }
                                                            })(function ($) {

                                                                var $html = $("html"),
                                                                        $window = $.mobile.window;

//remove initial build class (only present on first pageshow)
                                                                function hideRenderingClass() {
                                                                    $html.removeClass("ui-mobile-rendering");
                                                                }

// trigger mobileinit event - useful hook for configuring $.mobile settings before they're used
                                                                $.mobile.document.trigger("mobileinit");

// support conditions
// if device support condition(s) aren't met, leave things as they are -> a basic, usable experience,
// otherwise, proceed with the enhancements
                                                                if (!$.mobile.gradeA()) {
                                                                    return;
                                                                }

// override ajaxEnabled on platforms that have known conflicts with hash history updates
// or generally work better browsing in regular http for full page refreshes (BB5, Opera Mini)
                                                                if ($.mobile.ajaxBlacklist) {
                                                                    $.mobile.ajaxEnabled = false;
                                                                }

// Add mobile, initial load "rendering" classes to docEl
                                                                $html.addClass("ui-mobile ui-mobile-rendering");

// This is a fallback. If anything goes wrong (JS errors, etc), or events don't fire,
// this ensures the rendering class is removed after 5 seconds, so content is visible and accessible
                                                                setTimeout(hideRenderingClass, 5000);

                                                                $.extend($.mobile, {
                                                                    // find and enhance the pages in the dom and transition to the first page.
                                                                    initializePage: function () {
                                                                        // find present pages
                                                                        var pagecontainer,
                                                                                path = $.mobile.path,
                                                                                $pages = $(":jqmData(role='page'), :jqmData(role='dialog')"),
                                                                                hash = path.stripHash(path.stripQueryParams(path.parseLocation().hash)),
                                                                                theLocation = $.mobile.path.parseLocation(),
                                                                                hashPage = hash ? document.getElementById(hash) : undefined;

                                                                        // if no pages are found, create one with body's inner html
                                                                        if (!$pages.length) {
                                                                            $pages = $("body").wrapInner("<div data-" + $.mobile.ns + "role='page'></div>").children(0);
                                                                        }

                                                                        // add dialogs, set data-url attrs
                                                                        $pages.each(function () {
                                                                            var $this = $(this);

                                                                            // unless the data url is already set set it to the pathname
                                                                            if (!$this[ 0 ].getAttribute("data-" + $.mobile.ns + "url")) {
                                                                                $this.attr("data-" + $.mobile.ns + "url", $this.attr("id") ||
                                                                                        path.convertUrlToDataUrl(theLocation.pathname + theLocation.search));
                                                                            }
                                                                        });

                                                                        // define first page in dom case one backs out to the directory root (not always the first page visited, but defined as fallback)
                                                                        $.mobile.firstPage = $pages.first();

                                                                        // define page container
                                                                        pagecontainer = $.mobile.firstPage.parent().pagecontainer();

                                                                        // initialize navigation events now, after mobileinit has occurred and the page container
                                                                        // has been created but before the rest of the library is alerted to that fact
                                                                        $.mobile.navreadyDeferred.resolve();

                                                                        // cue page loading message
                                                                        $.mobile.loading("show");

                                                                        //remove initial build class (only present on first pageshow)
                                                                        hideRenderingClass();

                                                                        // if hashchange listening is disabled, there's no hash deeplink,
                                                                        // the hash is not valid (contains more than one # or does not start with #)
                                                                        // or there is no page with that hash, change to the first page in the DOM
                                                                        // Remember, however, that the hash can also be a path!
                                                                        if (!($.mobile.hashListeningEnabled &&
                                                                                $.mobile.path.isHashValid(location.hash) &&
                                                                                ($(hashPage).is(":jqmData(role='page')") ||
                                                                                        $.mobile.path.isPath(hash) ||
                                                                                        hash === $.mobile.dialogHashKey))) {

                                                                            // make sure to set initial popstate state if it exists
                                                                            // so that navigation back to the initial page works properly
                                                                            if ($.event.special.navigate.isPushStateEnabled()) {
                                                                                $.mobile.navigate.navigator.squash(path.parseLocation().href);
                                                                            }

                                                                            pagecontainer.pagecontainer("change", $.mobile.firstPage, {
                                                                                transition: "none",
                                                                                reverse: true,
                                                                                changeUrl: false,
                                                                                fromHashChange: true
                                                                            });
                                                                        } else {
                                                                            // trigger hashchange or navigate to squash and record the correct
                                                                            // history entry for an initial hash path
                                                                            if (!$.event.special.navigate.isPushStateEnabled()) {
                                                                                $window.trigger("hashchange", [true]);
                                                                            } else {
                                                                                // TODO figure out how to simplify this interaction with the initial history entry
                                                                                // at the bottom js/navigate/navigate.js
                                                                                $.mobile.navigate.history.stack = [];
                                                                                $.mobile.navigate($.mobile.path.isPath(location.hash) ? location.hash : location.href);
                                                                            }
                                                                        }
                                                                    }
                                                                });

                                                                $(function () {
                                                                    //Run inlineSVG support test
                                                                    $.support.inlineSVG();

                                                                    // check which scrollTop value should be used by scrolling to 1 immediately at domready
                                                                    // then check what the scroll top is. Android will report 0... others 1
                                                                    // note that this initial scroll won't hide the address bar. It's just for the check.

                                                                    // hide iOS browser chrome on load if hideUrlBar is true this is to try and do it as soon as possible
                                                                    if ($.mobile.hideUrlBar) {
                                                                        window.scrollTo(0, 1);
                                                                    }

                                                                    // if defaultHomeScroll hasn't been set yet, see if scrollTop is 1
                                                                    // it should be 1 in most browsers, but android treats 1 as 0 (for hiding addr bar)
                                                                    // so if it's 1, use 0 from now on
                                                                    $.mobile.defaultHomeScroll = (!$.support.scrollTop || $.mobile.window.scrollTop() === 1) ? 0 : 1;

                                                                    //dom-ready inits
                                                                    if ($.mobile.autoInitializePage) {
                                                                      //  $.mobile.initializePage();
                                                                    }

                                                                    if (!$.support.cssPointerEvents) {
                                                                        // IE and Opera don't support CSS pointer-events: none that we use to disable link-based buttons
                                                                        // by adding the 'ui-disabled' class to them. Using a JavaScript workaround for those browser.
                                                                        // https://github.com/jquery/jquery-mobile/issues/3558

                                                                        // DEPRECATED as of 1.4.0 - remove ui-disabled after 1.4.0 release
                                                                        // only ui-state-disabled should be present thereafter
                                                                        $.mobile.document.delegate(".ui-state-disabled,.ui-disabled", "vclick",
                                                                                function (e) {
                                                                                    e.preventDefault();
                                                                                    e.stopImmediatePropagation();
                                                                                }
                                                                        );
                                                                    }
                                                                });
                                                            });

                                                        }));