/**
 * Created by thuy on 13/08/2017.
 */
define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/grid/provider'
], function ($,_, utils,Provider) {
    'use strict';

    return Provider.extend({

        /**
         * Reload grid
         * @returns {exports}
         */
        assignSelected: function () {
            var self = this;
            var selections = arguments[1];
            var saveUrl = self.save_url;
            var form_key = window.FORM_KEY;
            selections.form_key = form_key;
            if(window.location.href.indexOf('/id/')===-1){
                selections.template = window.location.href.substring(window.location.href.indexOf('/template/')+10,window.location.href.indexOf('/key/'));
            }
            else selections.template = window.location.href.substring(window.location.href.indexOf('/id/')+4,window.location.href.indexOf('/key/'));
            console.log(form_key);

            $.ajax({
                url: saveUrl,
                data: selections,
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                success: function (res) {
                    console.log(res);
                    $('body').trigger('processStop');
                    return this;
                }
            });
        }

    });
});
