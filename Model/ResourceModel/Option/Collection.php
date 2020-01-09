<?php
/**
 * advanced product option collection
 *
 * @author Squiz Pty Ltd <products@squiz.net>
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\Option;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\Option', 'Magenest\AdvancedProductOption\Model\ResourceModel\Option');
    }//end _construct()
}//end class
