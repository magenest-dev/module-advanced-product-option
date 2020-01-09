define(
    [
        'jquery',
        'ko',
    ],
    function ($, ko) {
        return function (data) {
            var self = this;
            return {
                id : ko.observable(data.id),
                is_new  : ko.observable(data.is_new),
                row_id  : ko.observable(data.row_id ),
                price : ko.observable(data.price),
                customer_group : ko.observable(data.customer_group),
                min_qty : ko.observable(data.min_qty),
                max_qty : ko.observable(data.max_qty),
                website_id : ko.observable(data.website_id),
                customer_group : ko.observable(data.customer_group),
            };
        }
    }
);

