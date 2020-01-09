<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/12/2016
 * Time: 23:54
 */
namespace Magenest\AdvancedProductOption\Block\Order\Item\Renderer;

use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer as Renderer;

class DefaultRenderer extends Renderer
{
    protected $_template = 'Magenest_AdvancedProductOption::order/items/renderer/default.phtml';

    public function getAdvancedProductOption()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

        $logger =  $objectManager->create('Magento\Framework\Logger\Monolog');
        $helper =  $objectManager->create('Magenest\AdvancedProductOption\Helper\Data');

        $item        = $this->getItem();
        $htmlOption = $helper->renderOption($item);
        return $htmlOption;
    }
}
