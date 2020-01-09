/**
 * Created by root on 07/12/2016.
 */
define(function () {
    'use strict';

    return function (target) {
        // modify target
        var reloadPrice = target.reloadPrice;
        target.reloadPrice = function() {
            console.log("hello");
        };
        return target;
    };
});