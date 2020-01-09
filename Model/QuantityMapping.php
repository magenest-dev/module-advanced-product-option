<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 16/01/2017
 * Time: 21:00
 */
namespace Magenest\AdvancedProductOption\Model;

class QuantityMapping extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\QuantityMapping');
    }
}
