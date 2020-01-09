<?php

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magenest\AdvancedProductOption\Model\ResourceModel\Template\CollectionFactory;
use Magenest\AdvancedProductOption\Model\OptionFactory;
use Magenest\AdvancedProductOption\Model\ProductFactory;
use Magenest\AdvancedProductOption\Model\QuantityMappingFactory;
use Magenest\AdvancedProductOption\Model\RowFactory;
use Magenest\AdvancedProductOption\Model\TierPriceFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    protected $_filter;
    protected $_templateFactory;
    protected $_optionFactory;
    protected $_productFactory;
    protected $_quantityMappingFactory;
    protected $_rowFactory;
    protected $_tierPriceFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        CollectionFactory $templateFactory,
        OptionFactory $optionFactory,
        ProductFactory $productFactory,
        QuantityMappingFactory $quantityMappingFactory,
        RowFactory $rowFactory,
        TierPriceFactory $tierPriceFactory,
        Registry $registry,
        Filter $filter
    ) {
        $this->_filter = $filter;
        parent::__construct($context);
        $this->_templateFactory = $templateFactory;
        $this->_optionFactory = $optionFactory;
        $this->_productFactory = $productFactory;
        $this->_quantityMappingFactory = $quantityMappingFactory;
        $this->_rowFactory = $rowFactory;
        $this->_tierPriceFactory = $tierPriceFactory;
    }
    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_templateFactory->create());
        $deleted = 0;
        if ($collection) {
            foreach ($collection->getItems() as $item) {
                $item->delete();
                $deleteOptions = $this->_optionFactory->create()->getCollection()->addFieldToFilter("template_id",$item->getData("template_id"));
                foreach ($deleteOptions as $deleteOption){
                    $deleteRows = $this->_rowFactory->create()->getCollection()->addFieldToFilter("option_id", $deleteOption["option_id"]);
                    foreach ($deleteRows as $deleteRow){
                        $this->_tierPriceFactory->create()->getCollection()->addFieldToFilter("row_id", $deleteRow["row_id"])->walk("delete");
                        $this->_rowFactory->create()->load($deleteRow["row_id"])->delete();
                    }
                    $this->_optionFactory->create()->load($deleteOption["option_id"])->delete();
                    $this->_productFactory->create()->getCollection()->addFieldToFilter("template_id",$deleteOption["template_id"])->walk("delete");
                }
                $deleted++;
            }
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('advancedproductoption/*/index');
    }
}
