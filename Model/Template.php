<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/05/2016
 * Time: 23:36
 */
namespace Magenest\AdvancedProductOption\Model;

class Template extends \Magento\Framework\Model\AbstractModel
{

    protected $optionFactory;

    protected $_storeManager;

    protected $_customerSession;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\AdvancedProductOption\Model\OptionFactory $optionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $theResource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $theResource, $resourceCollection, $data);
        $this->_customerSession = $customerSession;
        $this->_storeManager    = $storeManager;
        $this->optionFactory    = $optionFactory;
    }//end __construct()


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\Template');
    }//end _construct()


    /**
     * @return array
     */
    public function getOptionsWithFullInfo()
    {
        $options           = [];
        $templateId        = $this->getId();
        $optionsCollection = $this->optionFactory->create()->getCollection()->addFieldToFilter('template_id', $templateId);
        if ($optionsCollection->getSize() > 0) {
            /**
             *  @var \Magenest\AdvancedProductOption\Model\Option $optionModel
             */
            foreach ($optionsCollection as $optionModel) {
                /** @var \Magenest\AdvancedProductOption\Model\Option $optionModel */
                $options[] = $optionModel->loadFullInfo();
            }
        }

        return $options;
    }//end getOptionsWithFullInfo()


    /**
     * @return array
     */
    public function getOptionsWithFullInfoFrontend()
    {
        $customerGroupId   = $this->_customerSession->getCustomerGroupId();
        $websiteId         = $this->_storeManager->getStore()->getWebsiteId();
        $options           = [];
        $templateId        = $this->getId();
        $optionsCollection = $this->optionFactory->create()->getCollection()->addFieldToFilter('template_id', $templateId);
        if ($optionsCollection->getSize() > 0) {
            /*
                @var  $optionModel \Magenest\AdvancedProductOption\Model\Option
            */
            foreach ($optionsCollection as $optionModel) {
                $optionTemp = $optionModel->loadFullInfo();
                if (array_key_exists('rowsi', $optionTemp) && $optionTemp['rowsi'] != null) {
                    $optionRowsTemp = [];
                    foreach ($optionTemp['rowsi'] as $row) {
                        if (array_key_exists('tiersi', $row)) {
                            $rowTiersTemp = [];
                            foreach ($row['tiersi'] as $rowTiersItem) {
                                if ($rowTiersItem['customer_group'] != 32000) {
                                    if (($rowTiersItem['website_id'] == $websiteId || $rowTiersItem['website_id'] == 0) && $rowTiersItem['customer_group'] == $customerGroupId) {
                                        $rowTiersTemp[] = $rowTiersItem;
                                    }
                                } else {
                                    if ($rowTiersItem['website_id'] == $websiteId || $rowTiersItem['website_id'] == 0) {
                                        $rowTiersTemp[] = $rowTiersItem;
                                    }
                                }
                            }

                            $row['tiersi'] = $rowTiersTemp;
                        }

                        $optionRowsTemp[] = $row;
                    }

                    $optionTemp['rowsi'] = $optionRowsTemp;
                }//end if

                $options[] = $optionTemp;
            }//end foreach
        }//end if

        return $options;
    }//end getOptionsWithFullInfoFrontend()
}//end class
