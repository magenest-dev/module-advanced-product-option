<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/12/2016
 * Time: 10:46
 */
namespace Magenest\AdvancedProductOption\Block\Adminhtml\Items\Column;

class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    public function toHtml()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

        $logger =  $objectManager->create('Magento\Framework\Logger\Monolog');
        $helper =  $objectManager->create('Magenest\AdvancedProductOption\Helper\Data');

        $item        = $this->getItem();
        $htmlOption = $helper->renderOption($item);

        $html      = parent::toHtml().$htmlOption;

        return $html;
    }//end toHtml()
}
