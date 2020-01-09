<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/05/2016
 * Time: 17:42
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel;

class Row extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_apo_row', 'row_id');
    }//end _construct()
}//end class
