<?php
namespace Magenest\AdvancedProductOption\Observer\Price;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Option implements ObserverInterface
{
    /**
     * @var \Magenest\AdvancedProductOption\Model\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magenest\AdvancedProductOption\Model\RowFactory
     */
    protected $rowFactory;

    /**
     * @var \Magenest\AdvancedProductOption\Model\TierPriceFactory
     */
    protected $tierFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;


    public function __construct(
        \Magenest\AdvancedProductOption\Model\OptionFactory $optionFactory,
        \Magenest\AdvancedProductOption\Model\RowFactory $rowFactoryFactory,
        \Magenest\AdvancedProductOption\Model\TierPriceFactory $tierPriceFactory,
        \Magento\Framework\App\RequestInterface $requestInterface
    )
    {
        $this->optionFactory = $optionFactory;
        $this->rowFactory = $rowFactoryFactory;
        $this->tierFactory = $tierPriceFactory;
        $this->_request = $requestInterface;
    }


    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
        $originalFinalPrice = $finalPrice;
        $buyRequestItem = $product->getCustomOption('info_buyRequest');
        if (is_object($buyRequestItem)) {
            $buyRequestItemArr = (array)json_decode($buyRequestItem->getValue());
            $qty = $buyRequestItemArr['qty'];
            $aop = [];
            if (isset($buyRequestItemArr['aop'])) {
                $aop = $buyRequestItemArr['aop'];
            }
            if ($aop) {
                $tierPrice = 0;
                $specialPrice = 0;
                $rowPrice = 0;
                $totalPrice = 0;
                foreach ($aop as $optionId => $rowValue){
                    if($rowValue){
                        $tierPriceChild = 0;
                        $specialPriceChild = 0;
                        $rowPriceChild = 0;
                        $optionBean = $this->optionFactory->create()->load($optionId);
                        $optionType = $optionBean->getType();
                        //
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
                        $enableSpecialDateDefaults = $scopeConfig->getValue('advancedproductoption/settings_special_price/mode_apply_special_price_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $specialDateFromDefault = $scopeConfig->getValue('advancedproductoption/settings_special_price/from_date_apply', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $specialDateToDefault = $scopeConfig->getValue('advancedproductoption/settings_special_price/to_date_apply', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                        $today = date("Y-m-d");

                        if($optionType === 'select' || $optionType === 'radio' || $optionType === 'dropdown'){
                            $rowBean = $this->rowFactory->create()->load($rowValue);
                            $priceType = $rowBean->getPriceType();
                            $calculate_type = $rowBean->getCalculateType();
                            $specialDateFrom = $rowBean->getSpecialDateFrom();
                            $specialDateTo = $rowBean->getSpecialDateTo();
                            if($calculate_type == 'subtraction'){
                                if((($rowBean->getSpecialPrice()*1) != 0 && $enableSpecialDateDefaults == 1 && $specialDateFromDefault <= $today && $specialDateToDefault >= $today) || (!empty($specialDateFrom) && !empty($specialDateTo) && $specialDateFrom <= $today && $specialDateTo >= $today)){
                                    if($priceType == 'percentage'){
                                        $specialPriceChild = - (floatval($rowBean->getSpecialPrice())*$originalFinalPrice)/100;
                                    }else{
                                        $specialPriceChild = - (floatval($rowBean->getSpecialPrice()));
                                    }
                                }
                                $rowPriceChild = - (floatval($rowBean->getPrice()));
                            }else{
                                if((($rowBean->getSpecialPrice()*1) != 0 && $enableSpecialDateDefaults == 1 && $specialDateFromDefault <= $today && $specialDateToDefault >= $today) || (!empty($specialDateFrom) && !empty($specialDateTo) && $specialDateFrom <= $today && $specialDateTo >= $today)){
                                    if($priceType == 'percentage'){
                                        $specialPriceChild = (floatval($rowBean->getSpecialPrice())*$originalFinalPrice)/100;
                                    }else{
                                        $specialPriceChild = floatval($rowBean->getSpecialPrice());
                                    }
                                }
                                $rowPriceChild = (floatval($rowBean->getPrice()));
                            }
                            $tierBean = $this->tierFactory->create()->getCollection();
                            $tierItems = $tierBean->addFieldToFilter('row_id',['eq'=>$rowValue])->getItems();
                            if(!empty($tierItems)){
                                if($specialPriceChild != 0){
                                    $tierPriceChild = $this->getTierPrice($tierItems,$qty,$specialPriceChild);
                                }else{
                                    $tierPriceChild = $this->getTierPrice($tierItems,$qty,$rowPriceChild);
                                }
                            }
                        }elseif($optionType === 'swatch' || $optionType === 'checkbox'){
                            if (!is_array($rowValue)) {
                                $options = explode(',', $rowValue);
                            } else {
                                $options = $rowValue;
                            }
                            if (count($options) > 0) {
                                foreach ($options as $option) {
                                    $rowTitle = $this->rowFactory->create()->load($option);
                                    $specialDateFrom = $rowTitle->getSpecialDateFrom();
                                    $specialDateTo = $rowTitle->getSpecialDateTo();
                                    $tierTitlePrice = 0;
                                    $rowTitlePrice = 0;
                                    $specialTitlePrice = 0;
                                    if($rowTitle->getId()){
                                        $priceType = $rowTitle->getPriceType();
                                        $calculate_type = $rowTitle->getCalculateType();
                                        if($calculate_type == 'subtraction'){
                                            if(($rowTitle->getSpecialPrice() != 0 && $enableSpecialDateDefaults == 1 && $specialDateFromDefault <= $today && $specialDateToDefault >= $today) || (!empty($specialDateFrom) && !empty($specialDateTo) && $specialDateFrom <= $today && $specialDateTo >= $today)){
                                                if($priceType == 'percentage'){
                                                    $specialTitlePrice = - (floatval($rowTitle->getSpecialPrice())*$originalFinalPrice)/100;
                                                }else{
                                                    $specialTitlePrice = - floatval($rowTitle->getSpecialPrice());
                                                }
                                            }
                                            $rowTitlePrice = - floatval($rowTitle->getPrice());
                                        }else{
                                            if(($rowTitle->getSpecialPrice() != 0 && $enableSpecialDateDefaults == 1 && $specialDateFromDefault <= $today && $specialDateToDefault >= $today) || (!empty($specialDateFrom) && !empty($specialDateTo) && $specialDateFrom <= $today && $specialDateTo >= $today)){
                                                if($priceType == 'percentage'){
                                                    $specialTitlePrice = (floatval($rowTitle->getSpecialPrice())*$originalFinalPrice)/100;
                                                }else{
                                                    $specialTitlePrice = floatval($rowTitle->getSpecialPrice());
                                                }
                                            }
                                            $rowTitlePrice = floatval($rowTitle->getPrice());
                                        }
                                        $tierBean = $this->tierFactory->create()->getCollection();
                                        $tierItems = $tierBean->addFieldToFilter('row_id',['eq'=>$option])->getItems();
                                        if(!empty($tierItems)){
                                            if($specialTitlePrice != 0){
                                                $tierTitlePrice = $this->getTierPrice($tierItems,$qty,$specialTitlePrice);
                                            }else{
                                                $tierTitlePrice = $this->getTierPrice($tierItems,$qty,$rowTitlePrice);
                                            }
                                        }
                                    }
                                    $tierPriceChild += $tierTitlePrice;
                                    $specialPriceChild += $specialTitlePrice;
                                    $rowPriceChild += $rowTitlePrice;
                                }
                            }
                        }
                        $specialPrice += $specialPriceChild;
                        $rowPrice += $rowPriceChild;
                        $tierPrice += $tierPriceChild;
                        if($tierPriceChild != 0){
                            $totalPrice += $tierPriceChild;
                        }elseif ($specialPriceChild !=0){
                            $totalPrice += $specialPriceChild;
                        }else{
                            $totalPrice += $rowPriceChild;
                        }
                    }
                }
                $finalPrice += $totalPrice;
            }
            // if there is a difference in origianal final price and final price, we adjust the price then
            if ($finalPrice != $originalFinalPrice) {
                $product->setData('final_price', $finalPrice);
            }
        }

    }

    /**
     * @param $rowId
     * @param $qty
     * @param $price
     * @return float|int
     */
    public function getTierPrice($tierItems, $qty, $price)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        /** @var \Magento\Catalog\Model\Product\Interceptor $product */
        $customerGroupId = $customerSession->getCustomerGroupId();
        $websiteId = $storeManager->getStore()->getWebsiteId();
        $tierPrice = 0;
        foreach ($tierItems as $tierItem) {
            $tierPriceChild = 0;
            $data = $tierItem->getData();
            if (($data['website_id'] == $websiteId || $data['website_id'] == 0) && ($data['customer_group'] == $customerGroupId || $data['customer_group'] == 32000)) {
                if ($data['min_qty'] <= $qty && $qty <= $data['max_qty']) {
                    $tierPriceType = $data['tier_price_type'];
                    $calculateTierType = $data['calculate_tier_type'];
                    if ($tierPriceType == 'percentage') {
                        if ($calculateTierType == 'subtraction') {
                            $tierPriceChild = -(floatval($data['price']) * $price) / 100;
                        } else {
                            $tierPriceChild = (floatval($data['price']) * $price) / 100;
                        }
                    } else {
                        if ($calculateTierType == 'subtraction') {
                            $tierPriceChild = -$data['price'];
                        } else {
                            $tierPriceChild = $data['price'];
                        }
                    }
                }
            }
            $tierPrice += $tierPriceChild;
        }
        return $tierPrice;
    }
}
