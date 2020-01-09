<?php
namespace Magenest\AdvancedProductOption\Plugin\Product;

class Options
{
    public function aroundCompareOptions(\Magento\Quote\Model\Quote\Item $subject,callable $proceed, $options1, $options2)
    {
        foreach ($options1 as $option) {
            $code = $option->getCode();
            if (!isset($options2[$code]) || strcmp($options2[$code]->getValue() , $option->getValue()) != 0) {
                return false;
            }
        }
        return true;
    }
}