<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/08/2016
 * Time: 13:37
 */

namespace Magenest\AdvancedProductOption\Model\ResourceModel;

class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct(){
        $this->_init('magenest_apo_product', 'id');
    }//end _construct()
}//end class
