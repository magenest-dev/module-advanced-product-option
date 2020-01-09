define(
    [
        'jquery',
        'ko',
    ],
    function ($, ko) {
        return function (data) {
            var self = this;
            return {
                id :ko.observable(data.id),
                option_id  : ko.observable(data.option_id),
                className: ko.observable(),
                css_thumbnail: ko.observable(data.css_thumbnail),
                isSelected  : ko.observable(false),
                parent_id  : ko.observable(data.parent_id),
                template_id  : ko.observable(data.template_id),
                is_new  : ko.observable('nochecked'),
                price : ko.observable(data.price),
                formatted_price: ko.observable(data.formatted_price),
                description : ko.observable(data.description),
                title : ko.observable(data.title),
                tooltip : ko.observable(data.tooltip),
                enableTooltip : ko.observable(data.enableTooltip),
                qty : ko.observable(data.qty),
                special_price : ko.observable(data.special_price),
                formatted_special_price : ko.observable(data.formatted_special_price),
                image: ko.observable(data.image),
                image_size: ko.observable(data.image_size),
                description : ko.observable(data.description),
                price_type : ko.observable(data.price_type),
                sku : ko.observable(data.sku),
                // Marcelo
                tiers : ko.observableArray(data.tiers),
                swatch : ko.observable(data.swatch),
                swatch_size: ko.observable(data.swatch_size),
                productImg : ko.observable(data.productImg),
                product_image_size: ko.observable(data.product_image_size),
                childrenOption : data.childrenOption,
                currency_symbol: data.currency_symbol,
                option_type: data.option_type,
                price_type: data.price_type,
                checkSelected: ko.observable(),
                priceDisplay: ko.observable(data.priceDisplay),
                calculate_type: ko.observable(data.calculate_type),

                symbol_calculate: ko.observable(data.symbol_calculate),
                apply_special_date_default: ko.observable(data.apply_special_date_default),


                special_date_from: ko.observable(data.special_date_from),
                special_date_to: ko.observable(data.special_date_to),
                enable_data_default: ko.observable(data.enable_data_default),
                special_date_from_default: ko.observable(data.special_date_from_default),
                special_date_to_default: ko.observable(data.special_date_to_default),

                init : function() {
                    var self = this;
                    self.hasToolTip = ko.computed(function() {
                        var tooltipValue = (self.tooltip());
                        if (tooltipValue == undefined || tooltipValue.length ==  0) {
                            return false;
                        } else {
                            return true;
                        }

                    }, self);
                    this.showSpecialPrice = ko.computed(function() {
                        var activeSpecialPriceValue = parseFloat(this.special_price());
                        var applySpecialDateDefault = parseFloat(this.apply_special_date_default());
                        if (activeSpecialPriceValue != 0 && applySpecialDateDefault == 1) {
                            return true;
                        } else {
                            return false;
                        }

                    }, this);
                    this.showSPrice = ko.computed(function() {
                        var activePriceValue = parseFloat(this.price());
                        if (activePriceValue > 0) {
                            return true;
                        } else {
                            return false;
                        }

                    }, this);
                    this.showCalculateType = ko.computed(function () {
                        var type = this.calculate_type();
                        if(type == 'subtraction'){
                            return '-';
                        }else{
                            return '+';
                        }
                    },this);
                    this.originPriceClass = ko.computed(function () {
                        var activeSpecialPriceValue = parseFloat(this.special_price());
                        var applySpecialDateDefault = parseFloat(this.apply_special_date_default());
                        if (activeSpecialPriceValue != 0 && applySpecialDateDefault == 1) {
                            return 'original-price-with-special-price';
                        } else {
                            return 'original-price-no-special-price';
                        }
                    },this);
                    // subscribe it by Aiden
                    this.isSelected.subscribe(function(newValue) {
                        ko.utils.arrayForEach(self.model.ao(),function(template) {
                            // if (template.id() == self.option_id() ) {
                            //update the selected options of it
                            //if the option  is in the list of template then do nothing
                            //otherwise add it on the
                            var isRowExist = false;
                            ko.utils.arrayForEach(template.selectedRows(), function(rowid) {
                                if(rowid == self.id()) {
                                    isRowExist = true;
                                }
                            });
                            //if the selected option is not in array then push it in the array
                            if (!isRowExist && newValue) {
                                template.selectedRows.push(self.id());
                            }
                            if (isRowExist && !newValue) {
                                var rowIdOfOption = self.id();
                                template.selectedRows.remove(rowIdOfOption);
                            }
                            // }
                        });

                    }, this);
                },

                // selectedOptions: ko.observableArray(),
                selectedSwatchOptions : ko.observableArray(),

                myRemove : function (row) {
                    self = row;
                    ko.utils.arrayForEach(self.model.ao(),function(template) {
                        if (template.id() == self.option_id() ) {
                            ko.utils.arrayForEach(template.selectedRows(), function(rowid) {
                                if(rowid == self.id()) {
                                    var rowIdOfOption = self.id();
                                    template.selectedRows.remove(rowIdOfOption);
                                }
                            });
                        }
                    });

                },
                /**
                 *
                 * @param row
                 * @returns {boolean}
                 */
                chooseOptionFromRow : function (row) {
                    var self = row;
                    row.isSelected(true);
                    var aop = "aop["+row.option_id._latestValue+"]";
                    if (self.option_type == 'radio') {
                        if (row.checkSelected()) {
                            $('input[type="radio"]').each(function (i) {
                                if (row.checkSelected() == $(this).val()) {
                                    $(this).attr('data-option', 'checked');
                                } else if (aop === this.name){
                                    $(this).attr('data-option', 'nochecked');
                                }
                            });
                        }
                    }
                    if(self.option_type == 'checkbox'){
                        if(row.checkSelected()){
                            $('input[type="checkbox"]').each(function (i) {
                                if(row.id()  == $(this).val()){
                                    $(this).attr('data-option','checked');
                                }
                            });
                        }else{
                            $('input[type="checkbox"]').each(function (i) {
                                if(row.id()  == $(this).val()){
                                    $(this).attr('data-option','nochecked');
                                }
                            });
                        }
                    }
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
                        if(atri == 'checked'){
                            var y = x+i;
                            rowIds[y] = $(this).val();
                        }
                    });
                    // self.updateOptionForModel(rowId);
                    self.calculateTotalPrice(rowIds);

                    rowIds = Array.from(new Set(rowIds));

                    if (rowIds.length == 0) {
                        var productPrice = 0;
                        self.price(0);
                    }
                    else {
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
                        if(flag){
                            var productPrice = self.model.totalPrice;
                        }
                        else{
                            var productPrice = 0;
                            self.price(0);
                        }

                    }

                    $('.price-box').trigger('updatePrice', {
                        'prices': {
                            'finalPrice': { 'amount': productPrice },
                            'basePrice': { 'amount': productPrice }
                        }
                    });
                    //show the depent option
                    ko.utils.arrayForEach(row.model.ao(), function(option) {
                        if (option.id() == row.option_id()) {
                            row.model.hideAllDependentOption(option);
                            row.model.getDependentOption(row);
                        }
                    });
                    return true;
                },
                calculateTotalPrice: function (rowIds) {
                    var self = this;
                    var totalPrice = 0;
                    if (typeof rowIds !== 'undefined' && rowIds.length > 0) {
                        ko.utils.arrayForEach(self.model.ao(), function (optionItem) {
                            var originalPrice = 0;
                            if (self.model.originProductPrice == -1) {
                                var priceHtml =  $('.product-info-price').find('span.price').html();
                                if(priceHtml === undefined || priceHtml === null || priceHtml.length == 0){
                                    alert('Please specify the quantity of product(s).');
                                    self.model.originProductPrice = 0;
                                }else{
                                    originalPrice =   priceHtml.match(/[\d\.]+$/)[0];
                                    self.model.originProductPrice = parseFloat(originalPrice);
                                }
                            }
                            var op = self.model.originProductPrice;
                            for (var i=0; i<rowIds.length; i++){
                                if(Array.isArray(rowIds[i])){
                                    ko.utils.arrayForEach(rowIds[i], function (id) {
                                        ko.utils.arrayForEach(optionItem.rows(), function (row) {
                                            if(row.id() == id){
                                                var price_type = row.price_type;
                                                if(row.special_price() != 0 && row.apply_special_date_default() == 1){
                                                    if(price_type == 'percentage'){
                                                        var per = ((row.special_price()*1)*op)/100;
                                                        if(row.calculate_type() == 'subtraction'){
                                                            totalPrice -= per;
                                                        }else {
                                                            totalPrice += per;
                                                        }
                                                    }else {
                                                        if(row.calculate_type() == 'subtraction'){
                                                            totalPrice -= (row.special_price())*1;
                                                        }else {
                                                            totalPrice += (row.special_price())*1;
                                                        }
                                                    }
                                                }else {
                                                    if(row.calculate_type() == 'subtraction'){
                                                        totalPrice -= (row.price())*1;
                                                    }else {
                                                        totalPrice += (row.price())*1;
                                                    }
                                                }
                                            }
                                        });
                                    });
                                }else {
                                    ko.utils.arrayForEach(optionItem.rows(), function (row) {
                                        if (row.id() === rowIds[i]) {
                                            var price_type = row.price_type;
                                            if(row.special_price() != 0 && row.apply_special_date_default() == 1){
                                                if(price_type == 'percentage'){
                                                    var per = ((row.special_price()*1)*op)/100;
                                                    if(row.calculate_type() == 'subtraction'){
                                                        totalPrice -= per;
                                                    }else {
                                                        totalPrice += per;
                                                    }
                                                }else {
                                                    if(row.calculate_type() == 'subtraction'){
                                                        totalPrice -= (row.special_price())*1;
                                                    }else {
                                                        totalPrice += (row.special_price())*1;
                                                    }
                                                }
                                            }else {
                                                if(row.calculate_type() == 'subtraction'){
                                                    totalPrice -= (row.price())*1;
                                                }else {
                                                    totalPrice += (row.price())*1;
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        });
                        this.model.totalPrice = totalPrice;
                    }
                },
                /**
                 * Deselect all option
                 */
                deSelectAllOption: function (rowInput) {
                    ko.utils.arrayForEach(this.model.ao(), function (template) {
                        if (template.id() == rowInput.option_id()) {
                            ko.utils.arrayForEach(template.rows(), function (row) {
                                row.isSelected(false);
                            });
                        }
                    });
                },

                /**
                 * update the options
                 */
                // updateOptionForModel:function(rowId) {
                //     ko.utils.arrayForEach(rowId, function (id) {
                //         ko.utils.arrayForEach(this.model.ao(), function(option) {
                //
                //         });
                //     });
                //     var currentOptionId = row.option_id();
                //     var currentSubOption = row.id();
                //     var isInArray = false;
                //     ko.utils.arrayForEach(this.model.selectedOptions(), function(option) {
                //         if (option != undefined) {
                //             if (option.id == currentOptionId) {
                //                 if (option.row_id != currentSubOption) {
                //                     option.row_id = currentSubOption;
                //                     //todo:issue
                //                     console.log('oooooooo');
                //                     option.price = row.price();
                //                 }
                //                 isInArray = true;
                //             }
                //         }
                //     });
                //     //if the select option is not in array then
                //     if (!isInArray) {
                //         if(row.special_price() != 0){
                //             var selectOption = {
                //                 id : currentOptionId,
                //                 row_id: currentSubOption,
                //                 price : row.formatted_special_price()
                //             }
                //         }else {
                //             var selectOption = {
                //                 id : currentOptionId,
                //                 row_id: currentSubOption,
                //                 price :row.price()
                //             }
                //         }
                //     }
                //     if (selectOption != undefined)
                //         this.model.selectedOptions.push(selectOption);
                // },
                /**
                 * calculate the price of the product
                 */
                calculatePrice : function(rowId) {
                    var originalPrice = 0;
                    if (this.model.originProductPrice == -1) {
                        var priceHtml =  $('.product-info-price').find('span.price').html();
                        if(priceHtml === undefined || priceHtml === null || priceHtml.length == 0){
                            alert('Please specify the quantity of product(s).');
                            this.model.originProductPrice = 0;
                        }else{
                            originalPrice =   priceHtml.match(/[\d\.]+$/)[0];
                            this.model.originProductPrice = parseFloat(originalPrice);
                        }
                    }
                    var op = this.model.originProductPrice;
                    var totalPrice = 0;
                    ko.utils.arrayForEach(this.model.ao(), function (template) {
                        ko.utils.arrayForEach(template.rows(), function (rows) {
                            ko.utils.arrayForEach(rowId, function (id) {
                                if(id == rows.id()){
                                    if(rows.special_price() > 0){
                                        if(rows.price_type == 'percentage'){
                                            totalPrice = totalPrice + ((op)*(rows.special_price()))/100;
                                        }else {
                                            totalPrice = totalPrice + (rows.special_price())*1;
                                        }
                                    }else{
                                        totalPrice = totalPrice + rows.price();
                                    }
                                }
                            });
                        });
                    });
                    this.model.totalPrice = totalPrice;
                },
                /**
                 *
                 * @param row
                 * @param event
                 * @returns {Row}
                 */
                removeSwatch : function(row, event) {
                    var self = this;
                    try {
                        var currentOptionId = row.id();
                        row.isSelected(false);
                        self.model.selectedSwatches(self.model.selectedSwatches() - 1);
                    } catch(err) {
                        console.log(err.message) ;
                    }
                    return this;
                },
                /**
                 *
                 * @param template
                 * @param event
                 */
                chooseSwatch : function(row, event) {
                    var self = this;
                    try {
                        if (self.model.selectedSwatches() == self.model.numberOfAllowedSwatches() && false) {
                            alert('cannot choose more than allowed!');
                        } else {
                            var currentOptionId = row.id();
                            row.isSelected(true);
                            var selector = 'select[' + 'data-option="' + currentOptionId + '"]';
                            var swatchObj = {
                                rowId: currentOptionId,
                                optionId: row.option_id(),
                                swatch: row.swatch(),
                                productImg: row.productImg()
                            };
                            var isInArray = false;
                            //if the swatch is already in array the pop it
                            //other wise push it
                            ko.utils.arrayForEach(self.selectedSwatchOptions(), function (item) {
                                if (item.rowId == currentOptionId) {
                                    isInArray = true;
                                }
                            });
                            if (!isInArray) {
                                self.selectedSwatchOptions.push(swatchObj);
                            }
                            self.model.selectedSwatches(self.model.selectedSwatches() + 1);
                        }
                    } catch(err) {
                        console.log(err.message) ;
                    }
                    return this;
                }
            };
        }
    }
);