/* 
 * robot.js | jQuery Plugin
 * Name: robotUtil
 * Description: Client side controller utils for robots
 * 
 * Pins
 * 17: right {on: 'fwd'}
 * 18: right {on: 'back'}
 * 27: left {on: 'fwd'}
 * 22: left {on: 'back'}
 */

(function ($, console) {
    'use strict';
    $.robotUtil = function (element, options) {
        var defaults = {
            pins: {
                left: {
                    fwd: 27,
                    back: 22
                },
                right: {
                    fwd: 17,
                    back: 18
                }
            },
            keys: {
                left: 37,
                right: 39,
                forward: 38,
                reverse: 40,
                stop: 13
            },
            url: 'http://192.168.1.113/ebot/',
            script: '',
            getStatus: function () {
                return 'Status';
            }
        },
        plugin = this,
        $element = $(element),

        log = function (msg, debug) {
            if (!debug) {
                console.log(msg);
            }
        },
        
        running = false,

        doRobot = function (pin, enable) {
            var toggle = (enable) ? 'on' : 'off',
                    ajaxUrl = plugin.settings.url;
            ajaxUrl = (plugin.settings.script) ? ajaxUrl + plugin.settings.script : ajaxUrl + pin + toggle + '.php';
            if (!running) {
                log('robot url: ' + ajaxUrl);
                running = true;
                $.ajax({
                    url: ajaxUrl,
                    async: false,
                    //dataType: 'json',
                    //data: {
                    //    toggle: enable,
                    //    pin: pin
                    //},
                    beforeSend: function () {
                        log('Sending...', true);
                    },
                    success: function () {
                        log('sucessful ajax...', true);
                    },
                    statusCode: {
                        404: function() {
                            log('Page not Found! ' + ajaxUrl);
                        }
                    },
                    error: function () {
                        log('ERROR ajax... ' + ajaxUrl);
                        running = false;
                    }
                }).done(function () {
                    log('Done Ajax', true);
                    running = false;
                });
            }
        },

        setupListeners = function () {
            log('setupListeners...', true);
            $element.keypress(function (event) {
                log('Key press: ' + event.which, true);
                if (event.which === plugin.settings.keys.stop) {
                    event.preventDefault();
                    plugin.stop();
                }
            });
            $element.keydown(function (event) {
                log('Key down: ' + event.which, true);
                if (event.which === plugin.settings.keys.forward) {
                    event.preventDefault();
                    plugin.forward();
                } else if (event.which === plugin.settings.keys.left) {
                    event.preventDefault();
                    plugin.left();
                } else if (event.which === plugin.settings.keys.right) {
                    event.preventDefault();
                    plugin.right();
                } else if (event.which === plugin.settings.keys.reverse) {
                    event.preventDefault();
                    plugin.reverse();
                }
            });
        };
        plugin.settings = {};

        // public methods
        plugin.init = function () {
            log('plugin init...', false);
            plugin.settings = $.extend({}, defaults, options);

            setupListeners();
        };

        plugin.right = function () {
            log('Right...');
            plugin.cleanup();
            doRobot(plugin.settings.pins.right.fwd, true);
        };

        plugin.left = function () {
            log('Left...');
            plugin.cleanup();
            doRobot(plugin.settings.pins.left.fwd, true);
        };

        plugin.forward = function () {
            log('Fwd...');
            plugin.cleanup();
            doRobot(plugin.settings.pins.right.fwd, true);
            doRobot(plugin.settings.pins.left.fwd, true);
        };

        plugin.reverse = function () {
            log('Reverse...');
            plugin.cleanup();
            doRobot(plugin.settings.pins.right.back, true);
            doRobot(plugin.settings.pins.left.back, true);
        };

        plugin.stop = function () {
            log('Stop...');
            plugin.cleanup();
        };

        plugin.cleanup = function () {
            doRobot(plugin.settings.pins.right.back, false);
            doRobot(plugin.settings.pins.left.back, false);
            doRobot(plugin.settings.pins.right.fwd, false);
            doRobot(plugin.settings.pins.left.fwd, false);
        };

        plugin.init();
    };

    $.fn.robotUtil = function (options) {
        return this.each(function () {
            if (undefined === $(this).data('robotUtil')) {
                var plugin = new $.robotUtil(this, options);
                $(this).data('robotUtil', plugin);
            }
        });
    };
}(jQuery, console));

$(window).robotUtil({
    url: 'http://localhost/dirtybeach.github.io/playground/robots/',
    script: 'index.php'
});
