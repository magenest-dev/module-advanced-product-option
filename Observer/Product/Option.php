<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 10/08/2016
 * Time: 10:37
 */
namespace Magenest\AdvancedProductOption\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Option implements ObserverInterface{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $product = $observer->getEvent()->getProduct();
        $product->setHasOptions(true);
    }//end execute()
}//end class
