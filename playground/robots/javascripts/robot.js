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
            keys : {
                left : 37,
                right : 39,
                forward : 38,
                reverse : 40,
                stop : 13
            },
            url : '/',
            script : '',
            getStatus : function () {
                return 'Status';
            }
        }, plugin = this, $element = $(element),

        log = function (msg, debug) {
            if ( !debug) {
                console.log(msg);
            }
        },

        running = false,

        doRobot = function (command) {
            var ajaxUrl = plugin.settings.url + plugin.settings.script,
                json = null;
            log('robot url: ' + ajaxUrl, true);
            $.ajax({
                url : ajaxUrl,
                type : 'get',
                data : {
                    ajax : 1,
                    type : 'car',
                    cmd : command
                },
                beforeSend : function () {
                    log('Sending...', true);
                },
                success : function (response) {
                    log('sucessful ajax...', true);
                    json = $.parseJSON(response); // create an object with the key of the array
                    log(json.content); // where content is the key of array that you want, $response['content']
                },
                statusCode : {
                    404 : function () {
                        log('Page not Found! ' + ajaxUrl);
                    }
                },
                error : function (response) {
                    log('ERROR ajax... ' + ajaxUrl);
                    json = $.parseJSON(response);
                    log(json.error);
                }
            }).done(function () {
                log('Done Ajax', true);
            });
        },

        setupListeners = function () {
            log('setupListeners...', true);
            $element.keypress(function (event) {
                log('Key press: ' + event.which, true);
                if (event.which === plugin.settings.keys.stop) {
                    event.preventDefault();
                    if (running) {
                        running = false;
                        plugin.stop();
                    } else {
                        running = true;
                        plugin.start();
                    }
                }
            });
            $element.keydown(function (event) {
                if (running) {
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
                } else {
                    log('Key down ignored. Robot not started! <<PRESS ENTER>>', true);
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

        plugin.start = function () {
            log('Started...');
            doRobot('start');
        };

        plugin.right = function () {
            log('Right...');
            doRobot('right');
        };

        plugin.left = function () {
            log('Left...');
            doRobot('left', true);
        };

        plugin.forward = function () {
            log('Fwd...');
            doRobot('forward');
        };

        plugin.reverse = function () {
            log('Reverse...');
            doRobot('back');
        };

        plugin.stop = function () {
            log('Stopped...');
            doRobot('stop');
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
    url : '',
    script : 'index.php'
});
