<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/05/2016
 * Time: 23:36
 */
namespace Magenest\AdvancedProductOption\Model;

class Row extends \Magento\Framework\Model\AbstractModel
{

    protected $tierFactory;

    protected $priceHelper;

    protected $currencyDirectory;

    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param RowFactory                                              $rowFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $theResource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AdvancedProductOption\Model\TierPriceFactory $tierPriceFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $theResource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $theResource, $resourceCollection, $data);
        $this->tierFactory       = $tierPriceFactory;
        $this->priceHelper       = $priceHelper;
        $this->currencyDirectory = $currency;
    }//end __construct()


    /**
     * @return void
     */
    protected function _construct(){
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\Row');
    }//end _construct()


    /**
     * @return array
     */
    public function getDependentOption()
    {
        $dependentOptionStr = $this->getData('children');
        $result =  explode(',', $dependentOptionStr);
        $out =  array_filter($result, function ($value) {
            return $value !== '';
        });
        return $out;
    }//end getDependentOption()


    /**
     * @return array
     */
    public function loadFullInfo(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $enableSpecialDateDefaults = $scopeConfig->getValue('advancedproductoption/settings_special_price/mode_apply_special_price_default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $specialDateFromDefault = $scopeConfig->getValue('advancedproductoption/settings_special_price/from_date_apply', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $specialDateToDefault = $scopeConfig->getValue('advancedproductoption/settings_special_price/to_date_apply', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $today = date("Y-m-d");

        $infos       = [];
        $optionId    = $this->getId();
        $infos       = $this->getData();
        $infos['id'] = $this->getId();

        $specialDateFrom = $this->getData('special_date_from');
        $specialDateTo = $this->getData('special_date_to');
        if(($enableSpecialDateDefaults == 1 && $specialDateFromDefault <= $today && $specialDateToDefault >= $today) || (!empty($specialDateFrom) && !empty($specialDateTo))){
            $infos['apply_special_date_default'] = 1;
        }elseif((!empty($specialDateFrom) && !empty($specialDateTo)) && ($specialDateFrom <= $today && $specialDateTo >= $today)){
            $infos['apply_special_date_default'] = 1;
        }else{
            $infos['apply_special_date_default'] = 0;
        }
        $specialPrice = $this->getData('special_price');
        $sPrice = 0;
        $price        = $this->getData('price');
        $priceType = $this->getData('price_type');
        if($priceType == 'percentage'){
            $infos['formatted_special_price'] = $specialPrice.'%';
        }else{
            $infos['formatted_special_price'] = $this->priceHelper->currency($specialPrice, true, false);
        }
        $infos['s_price'] = $sPrice;
        $infos['price_type'] = $priceType;
        $infos['formatted_price'] = $this->priceHelper->currency($price, true, false);
        $infos['currency_symbol'] = $this->getCurrencySymbol();
        $infos['id'] = $this->getId();
        $infos['childrenOption'] = explode(',', $this->getData('children'));
        $infos['css_thumbnail'] = $this->getOptionImageSize();
        $rowCollection = $this->tierFactory->create()->getCollection()->addFieldToFilter('row_id', $optionId);
        if ($rowCollection->getSize() > 0) {
            /**
             *@var  $rowModel \Magenest\AdvancedProductOption\Model\TierPrice
            */
            foreach ($rowCollection as $rowModel) {
                $infos['tiersi'][] = $rowModel->getInfo();
            }
        }

        return $infos;
    }//end loadFullInfo()

    public function getCurrencySymbol() {
        return $this->currencyDirectory->getCurrencySymbol();
    }

    /**
     * Get the children option of the row
     *
     * @return array
     */
    public function getChildrenOption(){
        if (!$this->getId()) {
            return;
        }
        $childrenOptionStringFormat = $this->getData('children');
        $result= explode(',', $childrenOptionStringFormat);
        $out =  array_filter($result, function ($value) {
            return $value !== '';
        });
        return $out;
    }//end getChildrenOption()

    public function getOptionImageSize(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $imageSize = $scopeConfig->getValue('advancedproductoption/settings_row/image_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $swatchImageSize = $scopeConfig->getValue('advancedproductoption/settings_row/swatch_image_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productImageSize = $scopeConfig->getValue('advancedproductoption/settings_row/product_image_size', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $dataOptionsImage = array(
            'image_size' => $imageSize,
            'swatch_size' => $swatchImageSize,
            'product_image_size' => $productImageSize
        );
        return $dataOptionsImage;
    }
}//end class