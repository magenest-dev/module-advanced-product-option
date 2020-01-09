<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/08/2016
 * Time: 13:38
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\Product;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\Product', 'Magenest\AdvancedProductOption\Model\ResourceModel\Product');
    }//end _construct()
}//end class
