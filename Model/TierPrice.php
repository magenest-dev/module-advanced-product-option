<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/05/2016
 * Time: 23:30
 */

namespace Magenest\AdvancedProductOption\Model;


class TierPrice extends \Magento\Framework\Model\AbstractModel{
    /**
     * @var \Magenest\AdvancedProductOption\Helper\Website
     */
    protected $websiteHelper;
    /**
     * @var  \Magenest\AdvancedProductOption\Helper\CustomerGroup
     */
    protected $customerGroupHelper;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currencyDirectory;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magenest\AdvancedProductOption\Helper\Website $websiteHelper,
        \Magenest\AdvancedProductOption\Helper\CustomerGroup $customerGroupHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $theResource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $theResource, $resourceCollection, $data);
        $this->websiteHelper       = $websiteHelper;
        $this->customerGroupHelper = $customerGroupHelper;
        $this->priceHelper       = $priceHelper;
        $this->currencyDirectory = $currency;
    }//end __construct()


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\TierPrice');
    }//end _construct()


    /**
     * Get the information of tier price
     *
     * @return mixed
     */
    public function getInfo(){
        $info                    = $this->getData();
        $price                   = $this->getData('price');
        $info['formatted_price'] = $this->priceHelper->currency($price, true, false);
        $info['website'] = $this->websiteHelper->getWebsites();
        $info['customergroup'] = $this->customerGroupHelper->getCustomerGroups();

        return $info;
    }//end getInfo()
}//end class
