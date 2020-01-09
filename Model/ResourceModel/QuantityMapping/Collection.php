<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 16/01/2017
 * Time: 22:05
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\QuantityMapping;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\QuantityMapping', 'Magenest\AdvancedProductOption\Model\ResourceModel\QuantityMapping');
    }
}
