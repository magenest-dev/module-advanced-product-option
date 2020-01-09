<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/05/2016
 * Time: 23:42
 */
namespace Magenest\AdvancedProductOption\Block\Adminhtml\Template;


class Edit extends \Magento\Backend\Block\Template
{

    protected $_template = 'option/edit.phtml';

    protected $coreRegistry;

    protected $templateFactory;

    protected $assignHelper;

    protected $storeManager;

    protected $currentFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AdvancedProductOption\Helper\Assign $assignHelper,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->templateFactory = $templateFactory;
        $this->assignHelper = $assignHelper;
        $this->storeManager = $context->getStoreManager();
        $this->currentFactory = $currencyFactory;

        parent::__construct($context, $data);

    }//end __construct()


    public function getTemplateId()
    {
        return $this->coreRegistry->registry('template_id');
    }//end getTemplateId()

    public function getCurrencySymbol() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currency = $this->currentFactory->create()->load($currencyCode);
        $currencySymbol = $currency->getCurrencySymbol();
        return $currencySymbol;
    }
    /**
     * get assigned product
     */
    public function getAssignedProducts()
    {
        $templateId = $this->getTemplateId();
        return $this->assignHelper->getAssignedProductByTemplateId($templateId);
    }

    public function getExcludeMode() {
        $model = $this->templateFactory ->create()->load($this->getTemplateId());
        $excludeMode = $model->getData('excludeMode');

        return $excludeMode;
    }
    /**
     * @return string
     */
    public function getTemplateTitle(){
        $id = $this->getTemplateId();
        if ($id) {
            $templateBean = $this->templateFactory->create()->load($id);
            return $templateBean->getTitle();
        } else {
            return '';
        }
    }//end getTemplateTitle()
    public function getCustomerGroup(){
        $output = [];
        return $output;
    }
    public function getWebsite(){
        $outputs = $this->_storeManager->getWebsites();
        $websites = array();
        $websites[] = array('website_id' => 0, 'name' => 'All Websites');
        foreach ($outputs as $output){
            $data = $output->getData();
            $websites[] = array('website_id' => $data['website_id'], 'name' => $data['name']);
        }
        return json_encode($websites);
    }


    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_block_html_before', ['block' => $this]);
        return parent::_toHtml();
    }//end _toHtml()
}//end class