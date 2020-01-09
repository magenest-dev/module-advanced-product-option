define(
    [
        'jquery',
        'ko',
    ],
    function ($, ko) {
        return function (data) {
            var self = this;
            return {
                title: ko.observable(data.title),
                id: ko.observable(data.id),
                is_new: ko.observable(data.is_new),
                type: ko.observable(data.type),
                tooltip: ko.observable(data.tooltip),
                active_tooltip: ko.observable(),
                active_tooltip_show: ko.observable(),
                is_required: ko.observable(data.is_required),
                special_price: ko.observable(data.special_price),
                activeImage: ko.observable(),
                dependent: ko.observable(data.dependent),
                isShown: ko.observable(data.isShown),
                image_size: ko.observable(data.image_size),
                swatch_size: ko.observable(data.swatch_size),
                product_image_size: ko.observable(data.product_image_size),
                //the dependent option is array for example [6,7]
                dependOptions: data.dependOptions,
                activeSpecialPrice: ko.observable(),
                activePrice: ko.observable(),
                //the absolute price of category
                absoluteActiveSpecialPrice: ko.observable(),
                absoluteActivePrice: ko.observable(),
                totalPrice: ko.observable(),
                activeTotalPrice: ko.observable(),
                template_id: ko.observable(data.template_id),
                checkSelected: ko.observable(),
                symbol_calculate: ko.observable(),

                init: function () {
                    var self = this;
                    self.className = ko.computed(function () {
                        var requiredValue = self.is_required();
                        if (requiredValue !== undefined) {
                            if (requiredValue == 1) {
                                return 'required-entry advanced-product-option-field';
                            }
                        }
                        return 'advanced-product-option-field'
                    }, self);
                    self.classNameRow = ko.computed(function () {
                        var requiredValue = self.is_required();
                        if (requiredValue !== undefined) {
                            if (requiredValue == 1) {
                                return 'required-entry advanced-product-row-field'
                            }
                        }
                        return 'advanced-product-row-field'
                    }, self);
                    //data validate required
                    self.dataRequired = ko.computed(function () {
                        var requiredValue = self.is_required();
                        if (requiredValue !== undefined) {
                            if (requiredValue == 1) {
                                return 'true';
                            }
                        }
                        return 'false'
                    }, self);
                    self.hasToolTip = ko.computed(function () {
                        var tooltipValue = (self.tooltip());
                        if (tooltipValue === undefined || tooltipValue === null || tooltipValue.length === 0) {
                            return false;
                        } else {
                            return true;
                        }
                    }, self);
                    //whether it has an active tooltip
                    self.hasActiveToolTip = ko.computed(function () {
                        var tooltipValue = (self.active_tooltip());
                        var showTooltip = (self.active_tooltip_show());
                        if (tooltipValue === undefined || tooltipValue === null || tooltipValue.length === 0 || showTooltip === false) {
                            return false;
                        } else {
                            return true;
                        }
                    }, self);
                    self.showSpecialPrice = ko.computed(function () {
                        // var activeSpecialPriceValue = parseFloat(self.absoluteActiveSpecialPrice());
                        var activeSpecialPriceValue = parseFloat(self.activeSpecialPrice())
                        if (activeSpecialPriceValue > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }, self);
                    self.showSPrice = ko.computed(function () {
                        // var arguments = this.__proto__.constructor.arguments;
                        var self = this;
                        var activePriceValue = parseFloat(self.absoluteActivePrice());
                        if (activePriceValue > 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }, self);
                },
                /**
                 * Choose the option used for select, checkbox, swatch
                 */
                chooseOption: function (option, row) {
                    var selector = 'advanced-product-option';
                    var rowIds = [];
                    $('.advanced-product-option-field').each(function (i) {
                        rowIds[i] = $(this).val();
                    });
                    var x = 0;
                    if (typeof rowIds !== 'undefined' && rowIds.length > 0) {
                        x = rowIds.length;
                    }
                    $('.advanced-product-row-field').each(function (i) {
                        var atri = $(this).attr('data-option');
                        if (atri == 'checked') {
                            var y = x + i;
                            rowIds[y] = $(this).val();
                        }
                    });
                    var self = option;
                    var totalPrice = 0;

                    rowIds = Array.from(new Set(rowIds));

                    if (rowIds.length == 1 && rowIds[0] == '') {
                        $('.price-box').trigger('updatePrice', {
                            'prices': {
                                'finalPrice': {'amount': 0},
                                'basePrice': {'amount': 0}
                            }
                        });
                        self.absoluteActivePrice(0);
                    }
                    else if (typeof rowIds !== 'undefined' && rowIds.length > 0) {
                        var flag = 0;
                        rowIds.forEach(function (element) {
                            if (element != null) {
                                if (typeof element == "object") {
                                    if (element[0] != "") {
                                        flag++;
                                    }
                                }
                                else if (typeof element == "string") {
                                    if (element != "") {
                                        flag++;
                                    }
                                }
                            }
                        });

                        if (flag) {

                            // self.activeImage(row.image());
                            ko.utils.arrayForEach(self.model.ao(), function (optionItem) {
                                var originalPrice = 0;
                                if (self.model.originProductPrice == -1) {
                                    var priceHtml = $('.product-info-price').find('span.price').html();
                                    if (priceHtml === undefined || priceHtml === null || priceHtml.length == 0) {
                                        alert('Please specify the quantity of product(s).');
                                        self.model.originProductPrice = 0;
                                    } else {
                                        originalPrice = priceHtml.match(/[\d\.]+$/)[0];
                                        self.model.originProductPrice = parseFloat(originalPrice);
                                    }
                                }
                                var op = self.model.originProductPrice;
                                for (var i = 0; i < rowIds.length; i++) {
                                    if (Array.isArray(rowIds[i])) {
                                        ko.utils.arrayForEach(rowIds[i], function (id) {
                                            ko.utils.arrayForEach(optionItem.rows(), function (row) {
                                                if (row.id() == id) {
                                                    var price_type = row.price_type;
                                                    if (row.special_price() != 0 && row.apply_special_date_default() == 1) {
                                                        if (price_type == 'percentage') {
                                                            var per = ((row.special_price() * 1) * op) / 100;
                                                            if (row.calculate_type() == 'subtraction') {
                                                                totalPrice -= per;
                                                            } else {
                                                                totalPrice += per;
                                                            }
                                                        } else {
                                                            if (row.calculate_type() == 'subtraction') {
                                                                totalPrice -= (row.special_price()) * 1;
                                                            } else {
                                                                totalPrice += (row.special_price()) * 1;
                                                            }
                                                        }
                                                    } else {
                                                        if (row.calculate_type() == 'subtraction') {
                                                            totalPrice -= (row.price()) * 1;
                                                        } else {
                                                            totalPrice += (row.price()) * 1;
                                                        }
                                                    }
                                                    self.absoluteActivePrice(row.price());
                                                    self.absoluteActiveSpecialPrice(row.special_price());
                                                    self.originPriceClass(row.originPriceClass());
                                                    self.model.getDependentOption(row);
                                                }
                                                self.totalPrice(totalPrice.toFixed(3));
                                                self.activeTotalPrice(row.currency_symbol + totalPrice.toFixed(3));
                                                $('.price-box').trigger('updatePrice', {
                                                    'prices': {
                                                        'finalPrice': {'amount': totalPrice},
                                                        'basePrice': {'amount': totalPrice}
                                                    }
                                                });
                                            });
                                        });
                                    } else {
                                        ko.utils.arrayForEach(optionItem.rows(), function (row) {
                                            if (row.id() === rowIds[i]) {
                                                var price_type = row.price_type;

                                                if (row.special_price() != 0 && row.apply_special_date_default() == 1) {
                                                    if (price_type == 'percentage') {
                                                        var per = ((row.special_price() * 1) * op) / 100;
                                                        if (row.calculate_type() == 'subtraction') {
                                                            totalPrice -= per;
                                                        } else {
                                                            totalPrice += per;
                                                        }
                                                    } else {
                                                        if (row.calculate_type() == 'subtraction') {
                                                            totalPrice -= (row.special_price()) * 1;
                                                        } else {
                                                            totalPrice += (row.special_price()) * 1;
                                                        }
                                                    }
                                                } else {
                                                    if (row.calculate_type() == 'subtraction') {
                                                        totalPrice -= (row.price()) * 1;
                                                    } else {
                                                        totalPrice += (row.price()) * 1;
                                                    }
                                                }
                                                self.absoluteActivePrice(row.price());
                                                self.absoluteActiveSpecialPrice(row.special_price());
                                                self.originPriceClass(row.originPriceClass());
                                                self.model.getDependentOption(row);

                                                self.totalPrice(totalPrice.toFixed(3));
                                                self.activeTotalPrice(row.currency_symbol + totalPrice.toFixed(3));
                                                $('.price-box').trigger('updatePrice', {
                                                    'prices': {
                                                        'finalPrice': {'amount': totalPrice},
                                                        'basePrice': {'amount': totalPrice}
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        }
                        else {
                            $('.price-box').trigger('updatePrice', {
                                'prices': {
                                    'finalPrice': {'amount': 0},
                                    'basePrice': {'amount': 0}
                                }
                            });
                            self.absoluteActivePrice(0);
                        }
                    }
                    ko.utils.arrayForEach(row, function (rowItem) {
                        for (var i = 0; i < rowIds.length; i++) {
                            if (rowItem.id() == rowIds[i]) {
                                self.activeImage(rowItem.image());
                                self.active_tooltip(rowItem.tooltip());
                                self.active_tooltip_show(rowItem.enableTooltip());
                                if (rowItem.special_price() != 0 && rowItem.apply_special_date_default() == 1) {
                                    self.activeSpecialPrice(rowItem.formatted_special_price());
                                    self.activePrice(rowItem.formatted_price());
                                } else {
                                    self.activePrice(rowItem.formatted_price());
                                }
                                if (rowItem.calculate_type() == 'subtraction') {
                                    self.symbol_calculate('-');
                                } else {
                                    self.symbol_calculate('+');
                                }
                            }
                        }
                    });
                },
                formatToPriceRaw: function (price) {
                    var priceRaw = price.match(/[\d\.]+$/)[0];
                    return priceRaw;
                },
                calculatePrice: function () {

                },
                selectedSwatchOptions: ko.observableArray(),
                rows: ko.observableArray(data.rows),
                selectedRows: ko.observableArray(),
                originPriceClass: ko.observable('special')
            };
        }
    }
);