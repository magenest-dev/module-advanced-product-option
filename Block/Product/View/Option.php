<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/07/2016
 * Time: 01:26
 */
namespace Magenest\AdvancedProductOption\Block\Product\View;

class Option extends \Magento\Catalog\Block\Product\View\AbstractView
{
    protected $templateFactory;

    protected $assignHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory,
        \Magenest\AdvancedProductOption\Helper\Assign $assignHelper,
        array $data = []
    ) {
        $this->templateFactory = $templateFactory;
        $this->assignHelper    = $assignHelper;
        $this->arrayUtils = $arrayUtils;

        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * get Advanced Product Option
     */
    public function getAdvancedProductOption($productId){
        $options = [];
        $templateIds = $this->assignHelper->getTemplate($productId);
        foreach ($templateIds as $templateId){
            $template   = $this->templateFactory->create()->load($templateId);
            $options[] = $template->getOptionsWithFullInfoFrontend();
        }
        return $json = \Zend_Json::encode($options);
    }

    /**
     * @return string
     */
    public function getJsLayout(){
        $productId = $this->getProduct()->getId();
        $this->jsLayout['components']['advancedoption']['config']['productId'] = $productId;
        $this->jsLayout['components']['advancedoption']['config']['feedUrl']   = $this->getUrl('advancedproductoption/template/feed', ['product' => $productId]);
        $this->jsLayout['components']['advancedoption']['config']['apoRawData']   = $this->getAdvancedProductOption($productId);
        $x = $this->jsLayout;
        return json_encode($this->jsLayout);
    }
    public function getProductId(){
        return $this->getProduct()->getId();
    }
}
