define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Option',
        'Row',
        'Tier',
        'mage/translate'
    ],
    function ($,
              _,
              Component,
              ko,
              Option,
              Row,
              Tier,
              $t) {
        'use strict';
        return Component.extend({
            defaults: {
                scope: 'advancedoption',
                template: 'Magenest_AdvancedProductOption/option',
            },
            originProductPrice: -1,
            totalPrice:0,
            selectedOptions:[],
            selectPrices : [],
            isLoading:true,
            ajaxUrlElement:'advancedproductoption/template/load/id/',
            numberOfAllowedSwatches:0,
            selectedSwatches:0,
            ao:[],

            /**
             * init data for the advanced product option
             */
            initObservable: function () {
                this._super()
                    .observe({
                        formId: "magenest-advanced-option",
                        isLoading:true,
                        selectedOptions:[],
                        ao:[],
                        numberOfAllowedSwatches:0,
                        selectedSwatches:0
                    });
                var originalPrice = 0;
                if (this.originProductPrice == -1) {
                    var priceHtml =  $('.product-info-price').find('span.price').html();
                    if(priceHtml === undefined || priceHtml === null || priceHtml.length == 0){
                        alert('Please specify the quantity of product(s).');
                        this.originProductPrice = 0;
                    }else{
                        originalPrice =   priceHtml.match(/[\d\.]+$/)[0];
                        this.originProductPrice = parseFloat(originalPrice);
                    }
                }
                var self = this;
                var productOptionFeedUrl =  this.feedUrl;
                var response = this.apoRawData;
                var multitemplateData =JSON.parse(response);//Data parsing becomes a JavaScript object
                var templateData = [];
                //converting data from the server to a suitable format for use in Knockout by ko.utils
                ko.utils.arrayForEach(multitemplateData, function (templateRecordArr) {
                    ko.utils.arrayForEach(templateRecordArr , function(optionRecord) {
                        templateData.push(optionRecord);
                    });
                });
                //the Aiden code
                var mappedTemplates = $.map(templateData, function(item) {
                    if (item.rowsi !== undefined)  {
                        ko.utils.arrayForEach(item.rowsi, function(row) {
                            if (row.tiersi !== undefined) {
                                ko.utils.arrayForEach(row.tiersi, function (tier) {
                                    if ( row.tiers === undefined)   row.tiers = new Array();
                                    row.tiers.push(new Tier(tier));
                                });
                            }
                            //process the children option of the row
                            if (row.children !== undefined && row.children !== null) {
                                var childrenStr = row.children;
                                var childrenOption =childrenStr.split(',');
                                row.childrenOption = childrenOption;
                            } else {
                                row.childrenOption =[];
                            }
                            //end of processing the children option of the row
                            if (item.rows === undefined){
                                item.rows = new Array();
                            }
                            if(row.option_type == undefined){
                                row.option_type = item.type;
                            }
                            if(row.special_price != 0 && row.apply_special_date_default == 1){
                                if (row.tiersi !== undefined) {
                                    row.priceDisplay = row.formatted_special_price;
                                    ko.utils.arrayForEach(row.tiersi, function (tier) {
                                        var min = tier.min_qty;
                                        var max = tier.max_qty;
                                        var price = tier.price;
                                        var type = tier.tier_price_type;
                                        var calculate = tier.calculate_tier_type;
                                        var symbol = '';
                                        if(calculate == 'subtraction'){
                                            symbol = '-';
                                        }else{
                                            symbol = '+';
                                        }
                                        if(type == 'percentage'){
                                            row.priceDisplay += " (Buy from "+min+" to "+max+" products, purchase each product with only "+symbol+price+symbol+" per product.)";
                                        }else {
                                            row.priceDisplay += " (Buy from "+min+" to "+max+" products, purchase each product with only "+symbol+row.currency_symbol+price+" per product.)";
                                        }
                                    });
                                }else {
                                    row.priceDisplay = row.formatted_special_price;
                                }
                            }else{
                                if (row.tiersi !== undefined) {
                                    row.priceDisplay = row.formatted_price;
                                    ko.utils.arrayForEach(row.tiersi, function (tier) {
                                        var min = tier.min_qty;
                                        var max = tier.max_qty;
                                        var price = tier.price;
                                        var type = tier.tier_price_type;
                                        var calculate = tier.calculate_tier_type;
                                        var symbol = '';
                                        if(calculate == 'subtraction'){
                                            symbol = '-';
                                        }else{
                                            symbol = '+';
                                        }
                                        if(type == 'percentage'){
                                            row.priceDisplay += " (Buy from "+min+" to "+max+" products, purchase each product with only "+symbol+price+"% per product.)";
                                        }else {
                                            row.priceDisplay += " (Buy from "+min+" to "+max+" products, purchase each product with only "+symbol+row.currency_symbol+price+" per product.)";
                                        }
                                    });
                                }else {
                                    row.priceDisplay = row.formatted_price;
                                }
                            }
                            if(row.css_thumbnail !== undefined && row.css_thumbnail !== null){
                                row.image_size = row.css_thumbnail.image_size;
                                row.swatch_size = row.css_thumbnail.swatch_size;
                                row.product_image_size = row.css_thumbnail.product_image_size;
                                item.image_size = row.css_thumbnail.image_size;
                                item.swatch_size = row.css_thumbnail.swatch_size;
                                item.product_image_size = row.css_thumbnail.product_image_size;
                            }
                            var theRow =  new Row(row);
                            theRow.init();
                            theRow.model = self;
                            item.rows.push(theRow);
                        } );
                    }
                    var theTem =  new Option(item);
                    theTem.init();
                    theTem.model = self;
                    return theTem;
                });
                self.ao(mappedTemplates);
                self.isLoading(false);
                self.numberOfAllowedSwatches.subscribe(function(newValue) {
                    if (newValue) {
                        ko.utils.arrayForEach(self.ao(), function (template) {
                            if (template.type() === 'swatch') {
                                ko.utils.arrayForEach(template.rows(), function (row) {
                                    row.isSelected(false);
                                    self.selectedSwatches(0);
                                });
                            }
                        });
                    }
                });
                return this;
            },
            // todayCurrent: function () {
            //     var today = new Date();
            //     var dd = today.getDate();
            //     var mm = today.getMonth()+1;
            //     var yyyy = today.getFullYear();
            //     today = yyyy + '-' + mm + '-' + dd;
            //     return today;
            // },
            /**
             *
             * @param option
             */
            hideAllDependentOption: function(option) {
                var self = this;
                //dependentOption is an array the option id  for example [6,7]
                var dependentOption = option.dependOptions;

                if (dependentOption !== undefined || dependentOption.length > 0) {
                    for (var i = 0; i< dependentOption.length; i++) {
                        ko.utils.arrayForEach(self.ao(), function(itemOption) {
                            if (itemOption.id() === dependentOption[i])   {
                                itemOption.isShown(false);
                            }

                        });

                    }
                }

                return this;
            },

            /**
             * @param Row
             */
            getDependentOption :function(row) {
                var self = this;
                var childrenOption = row.childrenOption;
                if (childrenOption !== undefined) {

                    for (var i = 0; i< childrenOption.length; i++) {
                        ko.utils.arrayForEach(self.ao(), function(itemOption) {
                            if (itemOption.id() === childrenOption[i])   {
                                itemOption.isShown(true);
                                console.log('show the option of ' + itemOption.id());
                            }

                        });

                    }

                }
                return this;
            },

            chooseOptionsss: function () {

            }
        });
    }
);