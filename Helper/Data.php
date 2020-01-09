<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/03/2016
 * Time: 10:22
 */
namespace Magenest\AdvancedProductOption\Helper;

use Magento\Framework\App\Helper\Context;
use Magenest\AdvancedProductOption\Model\RowFactory ;
use Magenest\AdvancedProductOption\Model\OptionFactory ;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $rowFactory;

    protected $optionFactory;

    public function __construct(
        Context $context,
        RowFactory $rowFactory,
        OptionFactory $optionFactory
    ) {
        parent::__construct($context);
        $this->rowFactory = $rowFactory;
        $this->optionFactory = $optionFactory;
    }
    /**
     * rendering the advanced product option on order
     * and in the frontend
     * @param
     */
    public function renderOption($item)
    {
        $html ='';
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->create('Magento\Framework\Logger\Monolog');

        $productRequestArr = $item->getProductOptionByCode('info_buyRequest');


        if (isset($productRequestArr['aop'])) {
            if (!empty($productRequestArr['aop'])) {
                foreach ($productRequestArr['aop'] as $optionId => $rowId) {
                    $optionBean = $this->optionFactory->create()->load($optionId);

                    $optionTitle = $optionBean->getData('title');
                    $rowBean = $this->rowFactory->create()->load($rowId);

                    $rowTitle = $rowBean->getData('title');

                    $optionType = $optionBean->getData('type');
                    switch ($optionType) {
                        case "radio":
                        case "select":
                        case "dropdown":
                            $html .= "<span class='option-product title'>" . $optionTitle . "</span>";
                            $html .= ":";

                            $html .= "<span class='option-product value'>" . $rowTitle . "</span>";
                            $html .= "<br>";

                            break;

                        case "text":
                        case "textarea":
                            $html .= "<span class='option-product title'>" . $optionTitle . "</span>";
                            $html .= ":";
                            $html .= "<span class='option-product value'>" . $rowId . "</span>";
                            $html .= "<br>";
                            break;
                    }
                }
            }
        }
        return $html;
    }
}//end class
