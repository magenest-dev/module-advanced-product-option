<?php
/**
 * advanced product option templates collection
 *
 * @author Squiz Pty Ltd <products@squiz.net>
 */
namespace Magenest\AdvancedProductOption\Model\ResourceModel\Template;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

	protected $_idFieldName = 'template_id';
    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\Template', 'Magenest\AdvancedProductOption\Model\ResourceModel\Template');
    }//end _construct()
}//end class
