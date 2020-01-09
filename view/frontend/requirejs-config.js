/**
 * Copyright © 2016 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            Option: 'Magenest_AdvancedProductOption/js/model/Option',
            Row: 'Magenest_AdvancedProductOption/js/model/Row',
            Tier: 'Magenest_AdvancedProductOption/js/model/Tier',
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/price-box': {
                'Magenest_AdvancedProductOption/js/pricebox/pluggin': true
            }
        }
    }
};