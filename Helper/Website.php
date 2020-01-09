<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/07/2016
 * Time: 20:25
 */

namespace Magenest\AdvancedProductOption\Helper;

class Website extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;


    /**
     * @param \Magento\Framework\App\HelperContext       $context
     * @param \Magento\Directory\Helper\Data             $directoryHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager    = $storeManager;
        $this->directoryHelper = $directoryHelper;
    }//end __construct()


    public function getWebsites()
    {
        $websitesArr = [
                        [
                         'label' => __('All Websites').' ['.$this->directoryHelper->getBaseCurrencyCode().']',
                         'value' => 0,
                        ],
                       ];

        $websitesList = $this->storeManager->getWebsites();
        foreach ($websitesList as $website) {
            /*
                @var \Magento\Store\Model\Website $website
            */
            $websitesArr[] = [
                              'label' => $website->getName().'['.$website->getBaseCurrencyCode().']',
                              'value' => $website->getId(),
                             ];
        }

        return $websitesArr;
    }//end getWebsites()
}//end class
