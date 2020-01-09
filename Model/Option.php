<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/07/2016
 * Time: 10:54
 */

namespace Magenest\AdvancedProductOption\Model;

class Option extends \Magento\Framework\Model\AbstractModel
{

    protected $rowFactory;

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
        \Magento\Directory\Model\Currency $currency,
        \Magenest\AdvancedProductOption\Model\RowFactory $rowFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $theResource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $theResource, $resourceCollection, $data);
        $this->rowFactory        = $rowFactory;
        $this->currencyDirectory = $currency;
    }//end __construct()


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\Option');
    }//end _construct()

    public function getParentOptionInfo()
    {

        $parentRows = [];
        if (!$this->getId()) {
            return;
        }
        $rowCollection = $this->rowFactory->create()->getCollection();
        if ($rowCollection->getSize() > 0) {

            /** @var \Magenest\AdvancedProductOption\Model\Row $rowModel */
            foreach ($rowCollection as $rowModel) {
                $children = $rowModel->getChildrenOption();
                if (in_array($this->getId(), $children)) {
                    $parentRows[] = $this->getId();
                }
            }
        }

        return $parentRows;
    }

    /**
     * @return array
     */
    public function loadFullInfo()
    {
        $infos    = [];
        $optionId = $this->getId();
        $infos    = $this->getData();

        $infos['id']       = $this->getId();
        $infos['currency'] = $this->currencyDirectory->getCurrencySymbol();
        // support dependency feature
        $parentRows  = $this->getParentOptionInfo();
        if (!empty($parentRows)) {
            $infos['dependent'] = true;
            $infos['isShown']   = false;
        } else {
            $infos['dependent'] = false;
            $infos['isShown']   = true;
        }

        $rowCollection = $this->rowFactory->create()->getCollection()->addFieldToFilter('option_id', $optionId);
        if ($rowCollection->getSize() > 0) {
            /*
                @var  $rowModel \Magenest\AdvancedProductOption\Model\Row
            */
            foreach ($rowCollection as $rowModel) {
                $infos['rowsi'][] = $rowModel->loadFullInfo();
            }
        }

        $infos['dependOptions'] = $this->getDependentOptions();
        return $infos;
    }//end loadFullInfo()




    /**
     * @return array
     */
    public function getDependentOptions()
    {
        $optionId      = $this->getId();
        $dependOptions = [];

        $rowCollection = $this->rowFactory->create()->getCollection()->addFieldToFilter('option_id', $optionId);
        if ($rowCollection->getSize() > 0) {
            /*
                @var  $rowModel \Magenest\AdvancedProductOption\Model\Row
            */
            foreach ($rowCollection as $rowModel) {
                // calculate the dependOptions by combination all the rows the dependent option
                $rowDependentOption = $rowModel->getDependentOption();

                if ($rowDependentOption) {
                    foreach ($rowDependentOption as $optionId) {
                        if (!in_array($optionId, $dependOptions)) {
                            $dependOptions[] = $optionId;
                        }
                    }
                }
            }
        }

        return $dependOptions;
    }//end getDependentOptions()
}//end class
