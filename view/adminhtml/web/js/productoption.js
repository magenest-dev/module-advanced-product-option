define([
    "jquery",
    "ko",
    "uiClass",
    'mage/url',
    'Magento_Ui/js/lib/spinner',
    "Magento_Ui/js/modal/modal",
    "Magenest_AdvancedProductOption/js/productoption",
    'jquery/file-uploader',
    'mage/loader',
    "underscore"
], function ($, ko,Class,url,spinner,modal, productoption,fileupload, loader,_) {
    "use strict";
    ko.bindingHandlers.aop = {
        /**
         * Scope binding's init method.
         * @returns {Object} - Knockout declaration for it to let binding control descendants.
         */
        init: function () {
            return {
                controlsDescendantBindings: true
            };
        }
    };
    ko.bindingHandlers.fileupload= {
        init: function(element, valueAccessor,allBindings, viewModel, bindingContext) {
            $(element).fileupload({  dataType: 'json',
                done: function (e, data) {
                    var imagePath = data.result.url;
                    viewModel.image(imagePath);
                    $(element).next('.spinner-wrapper').hide();
            }}).bind('fileuploadstart', function (event) {
                var inputElement = $(event.target);
                var tdElement = inputElement.parent();
                var tdOffset = $(tdElement).offset();
                var top = tdOffset.top;
                var left = tdOffset.left;

                var loaderEl  = inputElement.next('div');
                loaderEl.show();

                loaderEl.css('color','grey');
                loaderEl.css('background-color','grey');
                loaderEl.offset(tdOffset);

            });
        },
        update: function(element, valueAccessor,allBindings, viewModel, bindingContext) {

        }
    };
    ko.bindingHandlers.swatchupload= {

        init: function(element, valueAccessor,allBindings, viewModel, bindingContext) {
            $(element).fileupload({  dataType: 'json',
                done: function (e, data) {
                    var imagePath = data.result.url;
                    viewModel.swatch(imagePath);

                    $(element).next('.spinner-wrapper').hide();
            }}).bind('fileuploadstart', function (event) {

                var inputElement = $(event.target);
                var tdElement = inputElement.parent();
                var tdOffset = $(tdElement).offset();

                var top = tdOffset.top;
                var left = tdOffset.left;

                var loaderEl  = inputElement.next('div');
                loaderEl.show();
               /* loaderEl.css('position','absolute');
                loaderEl.css('top',top);
                loaderEl.css('left',left);*/
                loaderEl.css('color','grey');
                loaderEl.css('background-color','grey');
                loaderEl.offset(tdOffset);
            });
        },
        update: function(element, valueAccessor,allBindings, viewModel, bindingContext) {

        }
    };
    ko.bindingHandlers.productimgupload= {
        init: function(element, valueAccessor,allBindings, viewModel, bindingContext) {
            $(element).fileupload({  dataType: 'json',
                done: function (e, data) {
                    var imagePath = data.result.url;
                    viewModel.productImg(imagePath);
                    $(element).next('.spinner-wrapper').hide();
                }}).bind('fileuploadstart', function (event) {
                var inputElement = $(event.target);
                var tdElement = inputElement.parent();
                var tdOffset = $(tdElement).offset();

                var top = tdOffset.top;
                var left = tdOffset.left;

                var loaderEl  = inputElement.next('div');
                loaderEl.show();

                loaderEl.css('color','grey');
                loaderEl.css('background-color','grey');
                loaderEl.offset(tdOffset);



            });;
        },
        update: function(element, valueAccessor,allBindings, viewModel, bindingContext) {

        }
    };
    /* Template model */
    function Template(data) {
        var self = this;
        this.title = ko.observable(data.title);
        this.tooltip = ko.observable(data.tooltip);

        this.id  = ko.observable(data.id);
        this.is_new  = ko.observable(data.is_new);

        this.type = ko.observable(data.type);
        this.is_required = ko.observable(data.is_required);
       // this.is_required = ko.observable(data.is_required);

        this.multipleRow = ko.computed(function(){
            var isMultipleRow = false;
            if (this.type() == "select" || this.type() =='radio' || this.type() == 'multiselect' ||
                this.type() == 'checkbox' || this.type() =='swatch') {
                isMultipleRow = true;
            }

            return isMultipleRow;
        }, this);
        this.rows = ko.observableArray(data.rows);
    }
    function Row(data) {
        this.id  = ko.observable(data.id);
        this.option_id  = ko.observable(data.option_id);

        this.parent_id  = ko.observable(data.parent_id);
        this.template_id  = ko.observable(data.template_id);
        this.is_new  = ko.observable(data.is_new);

        this.price = ko.observable(data.price);
        this.description = ko.observable(data.description);
        this.title = ko.observable(data.title);

        this.qty = ko.observable(data.qty);
        this.special_price = ko.observable(data.special_price);

        this.image = ko.observable(data.image);

        this.childrenRaw = ko.observable(data.children);

        this.tooltip = ko.observable(data.tooltip);

        this.enableTooltip = ko.observable(data.enableTooltip);
        this.swatch = ko.observable(data.swatch);

        //if swatch change and there is corresponding product image then the the
        this.productImg = ko.observable(data.productImg);

        this.childrenOption = ko.observableArray(data.childrenOption);

        this.price_type = ko.observable(data.price_type);
        this.sku = ko.observable(data.sku);
        this.tiers = ko.observableArray(data.tiers);

        this.price_type_option = ko.observableArray(['fixed','percentage']);
        this.calculate_type_option = ko.observableArray(['addition','subtraction']);

        this.calculate_type = ko.observable(data.calculate_type);
        this.setOptionDisable = function(option,item) {
            //var value =
            var option_parent_id = $(option).parent().data('option-id');

            if (option_parent_id === option.value) {
                ko.applyBindingsToNode(option, {disable: true}, item);

            }
        }
        this.special_date_from = ko.observable(data.special_date_from);
        this.special_date_to = ko.observable(data.special_date_to);
    }
    function Tier(data) {
        if (data.id === undefined)  this.id =  ko.observable();
        this.id  = ko.observable(data.id);
        this.is_new  = ko.observable(data.is_new);
        this.row_id  = ko.observable(data.row_id );
        this.price = ko.observable(data.price);
        // this.customer_group = ko.observable(data.customer_group);
        this.min_qty = ko.observable(data.min_qty);
        this.max_qty = ko.observable(data.max_qty);
        this.tier_price_type_option = ko.observableArray(['fixed','percentage']);
        this.tier_price_type = ko.observable(data.tier_price_type);
        this.calculate_tier_type_option = ko.observableArray(['addition','subtraction']);
        this.calculate_tier_type = ko.observable(data.calculate_tier_type);
        this.website_id = ko.observable(data.website_id);
        this.website = ko.observableArray(data.website);
        this.customer_group = ko.observable(data.customer_group);
        this.customergroup = ko.observableArray(data.customergroup);//data.customergroup
    }
    function TemplateViewModel($config) {
        var self = this;
        self.config = $config;
        var loader = $('[data-role="loader"]').loader();
        self.loader  = loader;
        self.loader.show();
        self.loadOptionDone =ko.observable(false);
        self.loadWebsiteDataDone =  ko.observable(false);
        self.currencySymbol = ko.observable();
        self.selectedType = ko.observable();
        self.website = ko.observableArray([]);
        self.customergroup = ko.observableArray([]);
        self.maxRowId =10000;
        self.maxTierId =10000;
        self.loadOptionDone.subscribe(function(newValue) {
            if (newValue) {
                var another  = self.loadWebsiteDataDone();
                if (another) {
                    self.loader.hide();
                }
            }
        });
        self.loadWebsiteDataDone.subscribe(function(newValue) {
            if (newValue) {
                var another  = self.loadOptionDone();
                if (another) {
                    self.loader.hide();
                }
            }
        });
        self.templateoptions = ko.observableArray([]);
        self.availableType =  ko.observableArray(['select', 'checkbox','radio','text','textarea','swatch']);

        /**
         * add new option
         */
        self.addOption = function() {
            var optionIds  = $.map(self.templateoptions(), function(template){ return template.id(); });
            var maxId;
            if (optionIds.length == 0) {
                var maxId = 1;
            } else {
                var maxId = Math.max.apply(this, optionIds);
                maxId++;
            }
            self.maxRowId++;
            self.templateoptions.push(new Template(
                {
                    type: this.selectedType(),
                    id: maxId,
                    title: "Untitle",
                    is_new: 1,
                    rows :[new Row({title:'Untitle' , id: self.maxRowId, is_new: true,option_id:maxId})]

                }));

        };
        /**
         * Add Row to an option
         * @type {string|exports.defaults.loadTemplateUrl}
         */
        self.addRow = function(row,option_id) {
            var option = option_id;
            var optionId =  row;
            /*
                var maxId;
                var rowIds  = $.map(option.rows(), function(row){ return row.id(); });
                if (rowIds.length ==0) {
                    var maxId = 1;
                } else {
                    var maxId = Math.max.apply(this, rowIds);
                    maxId++;
                }
            */
            self.maxRowId++;
            //add new row to an option
            option.rows.push(new Row(
                {
                    option_id: optionId,
                    id: self.maxRowId,
                    is_new: 1
                }
            ));
        };
        /**
         * Delete Option
         * ================================
         */
        self.deleteOption = function(option) {
            ko.utils.arrayForEach(self.templateoptions(), function(item) {
                    if (item.id () == option.id()) {
                        self.templateoptions.destroy(item);
                    }

            });
        };
        /**
         * Delete Row
         */
        self.deleteRow = function(row) {
            ko.utils.arrayForEach(self.templateoptions(), function(option) {
                ko.utils.arrayForEach(option.rows(), function(item) {
                    if (item.id () == row.id()) {
                        option.rows.destroy(item);
                    }
                });

            });
        };
        /**
         * Delete Tier
         */
        self.deleteTier = function(tier) {
            ko.utils.arrayForEach(self.templateoptions(), function(option) {
                ko.utils.arrayForEach(option.rows(), function(row) {

                    ko.utils.arrayForEach(row.tiers(), function(item) {
                        if (tier.id() == item.id()) {
                            row.tiers.destroy(item);
                        }

                    });

                }) ;

            });
        };
        /**
         * Add Tier Price
         * @type {string|exports.defaults.loadTemplateUrl}
         */
        self.addTier = function (row,tier) {
            var row_id = row.id();
        /*    var maxId;

            var tierIds  = $.map(row.tiers(), function(tier){ return tier.id(); });
            if (tierIds.length ==0) {
                var maxId = 1;
            } else {
                var maxId = Math.max.apply(this, tierIds);
                maxId++;
            }*/
            self.maxTierId++;

            //add new row to an option
            row.tiers.push(new Tier(
                {
                    row_id: row_id,
                    id:  self.maxTierId,
                    is_new: 1

                }));


        };
        /**
         * Upload image to the option
         */
        self.uploadImage = function(row) {
            var idOfElement = "row_id" + row.id();
         //   $("#"+ idOfElement).fileupload();
        };
        /**
         * Upload swatch to the option
         */
        self.uploadSwatch = function(row) {
            var idOfElement = "row_id" + row.id();
         //   $("#"+ idOfElement).fileupload();


        };
        /**
         * Upload image product to the option
         */
        self.uploadProductImg = function(row) {
            var idOfElement = "row_id" + row.id();
         //   $("#"+ idOfElement).fileupload();
        };
        /**
         * Remove the image
         * @param option
         */
        self.removeImage = function(option) {
           option.image('');
        };
        /**
         * Remove the image
         * @param option
         */
        self.removeSwatch = function(option) {
           option.swatch('');
        };
        /**
         * Remove the image
         * @param option
         */
        self.removeProductImg = function(option) {
           option.productImg('');
        };
        /**
         * binding the assign product
         */
        self.submitOptionTemplate = function() {
            var productIds ='';
            $(".col-massaction").find("input:checked").each(function()    {
                productIds +=$(this).val() + ',';
            }) ;

            $('#product_ids').val(productIds);
            $("#product_option_form").submit();
        };
        
        self.submitOptionTemplateAndEdit = function() {
            $('#saveandcontinueedit').val("true");
            $("#product_option_form").submit();
        };
        /**
         *
         * @type {string|exports.defaults.loadTemplateUrl}
         */
        var templateFeedUrl = self.config.loadTemplateUrl;
        self.currencySymbol(self.config.currencySymbol);
        $.ajax({
            url: templateFeedUrl,
            data : {form_key: FORM_KEY}
        }).done(function(response) {
            var templateData = response;
            var mappedTemplates = $.map(templateData, function(item) {
                if (item.is_required == '1') {
                    item.is_required = true;
                } else {
                    item.is_required = false;
                }
                if (item.rowsi != undefined)  {
                    ko.utils.arrayForEach(item.rowsi, function(row) {
                        if (row.tiersi != undefined) {
                            ko.utils.arrayForEach(row.tiersi, function (tier) {
                               if ( row.tiers == undefined)   row.tiers = new Array();
                               row.tiers.push(new Tier(tier));
                               self.website(tier.website);
                               self.customergroup(tier.customergroup);
                               self.selectedCustomerGroup = ko.observable(tier.customer_group);
                            });
                        }
                        //process the children option of each row
                        if (row.children !== undefined && row.children !== null) {
                            var childrenOptionIds = row.children.split(',');
                            row.childrenOption = childrenOptionIds;
                        } else {
                            row.childrenOption =[];
                        }
                        //end of process the children option
                        if (item.rows === undefined)  item.rows = new Array();
                        item.rows.push( new Row(row));
                    } );
                }
                return new Template(item)
            });

            self.templateoptions(mappedTemplates);
            self.loadOptionDone(true);
            // self.loadWebsiteDataDone(true);
        } );
        // /** get other meta data **/
        var websiteFeedUrl = self.config.metaDataUrl;
        $.ajax({
            url: websiteFeedUrl,
            data : {form_key: FORM_KEY}
        }).done(function(response) {
            var metaData = response;
            //todo : implement and remove pseudo code
            var websites = metaData.website;
            var mappedWebsites = $.map(websites, function(item) {
                return item;
            });
            self.website(mappedWebsites);
            var customerGroup = metaData.customergroup;
            var mappedCustomerGroup = $.map(customerGroup, function(item) {
                return item;
            });
            self.customergroup(mappedCustomerGroup);
            self.loadWebsiteDataDone(true);

        } );
    }
//////////////////////////////////////////////////
//////////////////////////////////////////////////
    return Class.extend({
        defaults: {
            /**
             * Initialized solutions
             */
            updateOption: true,
            config : {
                loadTemplateUrl :'',
                metaDataUrl:''
            }
        },
        /**
         * Constructor
         */
        initialize: function (config) {
            var self = this;
            this.initConfig(config);
            this.bindAction(self);
        },
        bindAction:function(self) {
            var  $config = self.config;
            ko.cleanNode(document.getElementById("advancedoptions"));
            ko.applyBindings(new TemplateViewModel($config),document.getElementById("advancedoptions"));
        },

    })
});