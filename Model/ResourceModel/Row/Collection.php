<?php
/**
 * advanced product option templates collection
 *
 * @author Squiz Pty Ltd <products@squiz.net>
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\Row;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\Row', 'Magenest\AdvancedProductOption\Model\ResourceModel\Row');
    }//end _construct()
}//end class
