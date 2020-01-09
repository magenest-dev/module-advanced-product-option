/**
 * Created by thuy on 14/08/2017.
 */
define([
    'underscore',
    'ko',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_,ko, Column) {
    'use strict';

    return Column.extend({

        initialize: function () {
            this._super()
                .initObservable()
                .initModules()
                .initStatefull()
                .initLinks()
                .initUnique();

            var items = this.rows();
            var self = this;

              var template_id = window.apo_template_id;
              var feed_url = self.feed_url;
              var my_product_ids = window.assignProducts;
              
              if (my_product_ids !== undefined || my_product_ids !== null || my_product_ids.length > 0) {
                  ko.utils.arrayForEach(my_product_ids, function (item) {
                      self.select(item, true);
                      self.excludeMode(window.apo_excludeMode);
                  });
              }

                return this;
        },

    });
});
