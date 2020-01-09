<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 16/01/2017
 * Time: 21:02
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel;

class QuantityMapping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_apo_quantity_mapping', 'id');
    }
}
