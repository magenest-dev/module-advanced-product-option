/**
 * Created by root on 27/07/2016.
 */
/**
 * Created by root on 10/06/2016.
 */
define([
    'jquery',
    'uiComponent',
     'priceBox',
    'ko'
], function ($,Component,pricebox,ko) {
    'use strict';
    console.log('main model');
    ko.bindingHandlers.aop = {

        /**
         * Scope binding's init method.
         * @returns {Object} - Knockout declaration for it to let binding control descendants.
         */
        init: function () {
            return {
                controlsDescendantBindings: false
            };
        }
    };
    /* Template model */

    function Template(data) {
        var self = this;
        this.title = ko.observable(data.title);
        this.id  = ko.observable(data.id);

        this.is_new  = ko.observable(data.is_new);

        this.type = ko.observable(data.type);
        console.log(this.type);
        this.is_required = ko.observable(data.is_required);
        // this.is_required = ko.observable(data.is_required);

        this.special_price = ko.observable(data.special_price);

        this.activeImage  = ko.observable();

        this.dependent  = ko.observable(data.dependent);
        this.isShown  = ko.observable(data.isShown);

        //the dependent option is array for example [6,7]
        this.dependOptions = data.dependOptions;

        this.activeSpecialPrice = ko.observable();
        this.activePrice = ko.observable();

        //the absolute price of category

        this.absoluteActiveSpecialPrice = ko.observable();
        this.absoluteActivePrice = ko.observable();

        //selected options  store array of row_id,the swatch image, the product image the swatch
        //it can be used for checkbox and multi select box,too

        this.selectedSwatchOptions = ko.observableArray();

        this.selectedRows = ko.observableArray();
        this.originPriceClass = ko.observable();

        this.showSpecialPrice = ko.computed(function() {

            var activeSpecialPriceValue = parseFloat(this.absoluteActiveSpecialPrice());

            if (activeSpecialPriceValue > 0) {
                return true;
            } else {
                return false;
            }

        }, this);


        this.showSPrice = ko.computed(function() {
            console.log(this.absoluteActivePrice());

            var activePriceValue = parseFloat(this.absoluteActivePrice());

            console.log('active price');
            console.log(activePriceValue);

            if (activePriceValue > 0) {
                return true;
            } else {
                return false;
            }

        }, this);

        this.multipleRow = ko.computed(function(){
            var isMultipleRow = false;
            if (this.type() == "select" || this.type() =='radio' || this.type() == 'multiselect') {
                isMultipleRow = true;
            }

            return isMultipleRow;
        }, this);

        this.rows = ko.observableArray(data.rows);


        this.chooseOptionForRadioAndCheckBox=function(row, event) {
            try {
                var self = this;
               var template = self;
                ko.utils.arrayForEach(self.model.ao(), function(template) {
                    if (self.id() == currentOptionId) {


                        self.model.hideAllDependentOption(template);

                        //loop through the template id to get the row 's option children
                        ko.utils.arrayForEach(template.rows(), function(row) {

                            if (row.id() == rowId)    {
                                console.log('find the row id ' + rowId);
                                self.activeImage(row.image());
                                self.activePrice(row.formatted_price());

                                self.activeSpecialPrice(row.formatted_special_price());

                                self.originPriceClass (row.originPriceClass());

                                //absolute price

                                self.absoluteActivePrice(row.price());
                                self.absoluteActiveSpecialPrice(row.special_price());

                                //find the option which is depend on rowId
                                //if found, change it isShown to true
                                self.model.getDependentOption(row);

                            }

                        });
                    }

                });
            }

            catch(err) {
                console.log(err.message) ;
            }

            return true;
        }

        /**
         *
         * @param template
         * @param event
         * @returns {Template}
         */
        this.chooseOption = function (template, event)
        {
            try {
                console.log('choose the option ');

                var self = this;
                if (this.mode) {
                    //console.log(this.model.ao.length);

                } else if (this.ao) {
                    // console.log(this.ao().length);
                }
                // console.log(self.ao.model.length);

                var currentOptionId = template.id();
                //the element that is clicked
                var selector = 'select[' + 'data-option="' + currentOptionId + '"]';
                var rowId =  $(selector).val();

                //console.log(rowId);
                // console.log('The currnet option that you click on is ' + currentOptionId);
                ko.utils.arrayForEach(self.model.ao(), function(template) {
                    if (template.id() == currentOptionId) {


                        self.model.hideAllDependentOption(template);

                        //loop through the template id to get the row 's option children
                        ko.utils.arrayForEach(template.rows(), function(row) {

                            if (row.id() == rowId)    {
                                console.log('find the row id ' + rowId);

                                self.activeImage(row.image());
                                self.activePrice(row.formatted_price());

                                self.activeSpecialPrice(row.formatted_special_price());

                                //absolute price

                                self.absoluteActivePrice(row.price());
                                self.absoluteActiveSpecialPrice(row.special_price());

                                self.originPriceClass (row.originPriceClass());


                                //find the option which is depend on rowId
                                //if found, change it isShown to true
                                self.model.getDependentOption(row);

                            }

                        });
                    }

                });
            } catch(err) {
                console.log(err.message) ;
            }

            return true;
        }

    }

    /*function Swatch(data) {
        this.row_id =ko.observable(data.row_id);
    }*/
    function Row(data) {
        this.id  = ko.observable(data.id);
        this.option_id  = ko.observable(data.option_id);

        this.isSelected  = ko.observable(false);

        this.parent_id  = ko.observable(data.parent_id);
        this.template_id  = ko.observable(data.template_id);
        this.is_new  = ko.observable(data.is_new);

        this.price = ko.observable(data.price);
        this.formatted_price = ko.observable(data.formatted_price);

        this.description = ko.observable(data.description);
        this.title = ko.observable(data.title);
        this.tooltip = ko.observable(data.tooltip);
        this.enableTooltip = ko.observable(data.enableTooltip);

        this.qty = ko.observable(data.qty);
        this.special_price = ko.observable(data.special_price);
        this.formatted_special_price = ko.observable(data.formatted_special_price);

        this.image = ko.observable(data.image);
        this.description = ko.observable(data.description);

        this.price_type = ko.observable(data.price_type);
        this.sku = ko.observable(data.sku);
        // Marcelo


        this.tiers = ko.observableArray(data.tiers);

        this.swatch = ko.observable(data.swatch);

        this.productImg = ko.observable(data.productImg);

        this.childrenOption = data.childrenOption;

        this.selectedSwatchOptions = ko.observableArray();

        this.showSpecialPrice = ko.computed(function() {

            var activeSpecialPriceValue = parseFloat(this.special_price());

            if (activeSpecialPriceValue > 0) {
                return true;
            } else {
                return false;
            }

        }, this);


        /////
        this.showSPrice = ko.computed(function() {

            var activePriceValue = parseFloat(this.price());

            if (activePriceValue > 0) {
                return true;
            } else {
                return false;
            }

        }, this);

        this.originPriceClass = ko.computed(function () {
            var activeSpecialPriceValue = parseFloat(this.special_price());

            if (activeSpecialPriceValue > 0) {
                return 'original-price-with-special-price';
            } else {
                return 'original-price-no-special-price';
            }
        },this);
        /**
         *
         * @param row
         * @param event
         * @returns {boolean}
         */
        this.chooseOptionForRadioAndCheckBox = function(row,event) {
            //this.model.numberOfAllowedSwatches(cn);
            this.deSelectAllOption(row);
            //deselect all
            row.isSelected(true);
           // pricebox.reloadPrice();

            this.updateOptionForModel(row);
            this.calculatePrice();

            var productPrice = this.model.totalPrice;

            $('.price-box').trigger('updatePrice', {
                'prices': {
                    'finalPrice': { 'amount': productPrice },
                    'basePrice': { 'amount': productPrice }
                }
            });

            //show the depent option

            ko.utils.arrayForEach(row.model.ao(), function(template) {
                if (template.id() == row.option_id()) {
                    row.model.hideAllDependentOption(template);
                    row.model.getDependentOption(row);
                }
            });
                return true;
        }

        /**
         * update the options
         */
        this.updateOptionForModel=function(row) {
            var currentOptionId = row.option_id();
            var currentSubOption = row.id();

            var isInArray = false;

            ko.utils.arrayForEach(this.model.selectedOptions(), function(option) {
                if (option != undefined) {
                if (option.id == currentOptionId) {
                    if (option.row_id != currentSubOption) {
                        option.row_id = currentSubOption;

                        //todo:issue
                        option.price = row.price();
                    }
                    isInArray = true;
                }
            }
            });

            //if the select option is not in array then

            if (!isInArray) {
                var selectOption = {
                    id : currentOptionId,
                    row_id: currentSubOption,
                    price :row.price()
                }
            }


            if (selectOption != undefined)
            this.model.selectedOptions.push(selectOption);
        }

        /**
         * calculate the price of the
         * product
         *
         */
        this.calculatePrice = function() {
            if (this.model.originProductPrice == -1) {
                var priceHtml =  $('.product-info-price').find('span.price').html();
                var originalPrice =   priceHtml.match(/[\d\.]+$/)[0];
                this.model.originProductPrice = parseFloat(originalPrice);
            }

            var totalPrice = 0 - this.model.originProductPrice;

            ko.utils.arrayForEach(this.model.selectedOptions(), function(option) {
                if (option != undefined)
                totalPrice = totalPrice + parseFloat(option.price) ;
            });

            this.model.totalPrice =totalPrice;
        }

        /**
         * Deselect all option
         */
        this.deSelectAllOption = function (rowInput) {

            ko.utils.arrayForEach(this.model.ao(), function (template) {

                if (template.id() == rowInput.option_id()) {
                        ko.utils.arrayForEach(template.rows(), function (row) {
                            row.isSelected(false);

                        });
                }

            });
        }

        /**
         *
         * @param row
         * @param event
         * @returns {Row}
         */
        this.removeSwatch = function(row, event) {
            var self = this;

            try {
                var currentOptionId = row.id();
                row.isSelected(false);
                self.model.selectedSwatches(self.model.selectedSwatches() - 1);
            } catch(err) {
                console.log(err.message) ;
            }
            return this;
        }
        /**
         *
         * @param template
         * @param event
         */
        this.chooseSwatch = function(row, event) {
            var self = this;
            try {
                if (self.model.selectedSwatches() == self.model.numberOfAllowedSwatches()) {
                    alert('cannot choose more than allowed!');
                } else {
                    var currentOptionId = row.id();
                    row.isSelected(true);

                    //console.log(currentOptionId);
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

        /**
         *
         * @param template
         * @param event
         */

        this.chooseOption = function (template, event) {
        }

        //subscribe it by Aiden
        this.isSelected.subscribe(function(newValue) {
            var self = this;

            // console.log('new value');
            // console.log(newValue);

            ko.utils.arrayForEach(self.model.ao(),function(template) {
                if (template.id() == self.option_id() ) {
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
                       // template.selectedRows.destroy(rowIdOfOption);
                        console.log(template.selectedRows());

                    }

                    //log it
                    console.log(template.id());
                    console.log('selected rows in an template');
                    console.log(template.selectedRows());

                }

            });

        }, this);
        //end of subscribe


    }
    function Tier(data) {
        if (data.id == undefined)  this.id =  ko.observable();
        this.id  = ko.observable(data.id);
        this.is_new  = ko.observable(data.is_new);
        this.row_id  = ko.observable(data.row_id );

        this.price = ko.observable(data.price);
        this.customer_group = ko.observable(data.customer_group);
        this.min_qty = ko.observable(data.min_qty);
        this.max_qty = ko.observable(data.max_qty);

        this.website_id = ko.observable(data.website_id);
        this.customer_group = ko.observable(data.customer_group);
    }
    return Component.extend({
        defaults: {
            template: 'Magenest_AdvancedProductOption/option'
        },
        dataScope: 'advancedoption',
        formId : 'magenest-advanced-option',

        originProductPrice: -1,
        totalPrice:0,
        selectedOptions:[],
        selectPrices : [],
        isLoading:true,
        ajaxUrlElement:'advancedproductoption/template/load/id/',
        numberOfAllowedSwatches:0,
        selectedSwatches:0,
        ao:[],
      /*  initialize: function () {
            this._super();
            this.ao = ko.observableArray([]);
           *//* this.ao = ko.observableArray([
                { title: "Bungle", type: "text",amount: 1 , self: this },
                { title: "George",  self: this,type: "select" ,options:[{name: "1 Month" , value: "1"},{name: "2 Month" , value: "2"}] },
                { title: "Zippy", type: "checkbox" , self: this}
            ]);*//*


        },*/
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

            var self = this;
            // console.log(this.productId);
            var options ;

            var productOptionFeedUrl =  this.feedUrl;

            $.ajax({
                url: productOptionFeedUrl

            }).done(function(response){
                //var templateData = ko.utils.parseJson(response);
                var templateData =response;

                var mappedTemplates = $.map(templateData, function(item) {
                    if (item.rowsi != undefined)  {
                        ko.utils.arrayForEach(item.rowsi, function(row) {
                            if (row.tiersi != undefined) {
                                ko.utils.arrayForEach(row.tiersi, function (tier) {
                                    if ( row.tiers == undefined)   row.tiers = new Array();
                                    row.tiers.push(new Tier(tier));

                                });
                            }

                            //process the children option of the row
                            if (row.children != undefined ) {
                                var childrenStr = row.children;
                                var childrenOption =childrenStr.split(',');

                                row.childrenOption = childrenOption;

                            } else {
                                row.childrenOption =[];
                            }
                            //end of processing the children option of the row
                            if (item.rows == undefined)  item.rows = new Array();
                            var theRow =  new Row(row);
                            theRow.model = self;
                            item.rows.push(theRow);

                        } );
                    }
                    var theTem =  new Template(item);
                    theTem.model = self;
                    return theTem;
                });

                console.log(mappedTemplates);
               // self.observableArray({ao:mappedTemplates});
              self.ao(mappedTemplates);
                self.isLoading(false);

            });

            self.numberOfAllowedSwatches.subscribe(function(newValue) {
                if (newValue) {
                    ko.utils.arrayForEach(self.ao(), function (template) {
                        if (template.type() == 'swatch') {
                            ko.utils.arrayForEach(template.rows(), function (row) {
                                row.isSelected(false);
                                self.selectedSwatches(0);
                                console.log('selected swatches:');
                                console.log(self.selectedSwatches());
                            });
                        }
                    });
                }
            });

            //subscribe the isLoading
            self.isLoading.subscribe(function (newValue) {
                if (!newValue) {
                  $('.advanced-product-option').css('display','block');

                  $('span[data-role="apo-spinner"]').hide();
                }
            } );

            return this;
        },
        /**
         *
         * @param template
         */
            //todo: complete it
        hideAllDependentOption: function(template) {
            var self = this;
            //dependentOption is an array the option id  for example [6,7]
            var dependentOption = template.dependOptions;

            if (dependentOption != undefined || dependentOption.length > 0) {
                for (var i = 0; i< dependentOption.length; i++) {
                    ko.utils.arrayForEach(self.ao(), function(template) {
                        if (template.id() ==dependentOption[i])   {
                            template.isShown(false);
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
            if (childrenOption != undefined) {

                for (var i = 0; i< childrenOption.length; i++) {
                    ko.utils.arrayForEach(self.ao(), function(template) {
                        if (template.id() ==childrenOption[i])   {
                            template.isShown(true);
                            console.log('show the option of ' + template.id());
                        }

                    });

                }

            }
           return this;
        },


        /**
         *
         * @param template
         * @param event
         * @returns {boolean}
         */
        chooseOption: function(template) {
            try {

                var self = this;
                if (this.mode) {
                    //console.log(this.model.ao.length);

                } else if (this.ao) {
                    // console.log(this.ao().length);
                }
                // console.log(self.ao.model.length);

                var currentOptionId = template.id();
                //the element that is clicked
                var selector = 'select[' + 'data-option="' + currentOptionId + '"]';
                var rowId =  $(selector).val();

                //console.log(rowId);
                // console.log('The currnet option that you click on is ' + currentOptionId);
                ko.utils.arrayForEach(self.ao(), function(template) {
                    if (template.id() == currentOptionId) {
                        //self.hideAllDependentOption(template);

                        //loop through the template id to get the row 's option children
                        ko.utils.arrayForEach(template.rows(), function(row) {

                            if (row.id() == rowId)    {
                                //find the option which is depend on rowId
                                //if found, change it isShown to true
                                self.getDependentOption(row);

                            }

                        });
                    }

                });
            } catch(err) {
                console.log(err.message) ;
            }

            return this;
        }

    });
});
