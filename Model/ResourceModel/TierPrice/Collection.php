<?php
/**
 * advanced product option templates collection
 *
 * @author Squiz Pty Ltd <products@squiz.net>
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\TierPrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\TierPrice', 'Magenest\AdvancedProductOption\Model\ResourceModel\TierPrice');
    }//end _construct()
}//end class
