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

class Save implements ObserverInterface
{
    /**
     * @var \Magenest\AdvancedProductOption\Model\QuantityMappingFactory
     */
    protected $_mappingFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magenest\AdvancedProductOption\Model\QuantityMappingFactory $mappingFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\Message\ManagerInterface $managerInterface
    )
    {
        $this->_mappingFactory = $mappingFactory;
        $this->_request = $requestInterface;
        $this->messageManager = $managerInterface;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try{
            $event = $this->_request->getParam("event");
            $mappings = $event['mapping_value']['mapping_value'];
        }
        catch(\Exception $ex){
            $mappings = [];
        }
        $productId = $observer->getevent()->getProduct()->getdata("entity_id");
        if($mappings == []){
            $this->_mappingFactory->create()->getCollection()->addFieldToFilter("product_id",$productId)->walk("delete");
        }
        else{
            $quantity = array_column($mappings,"quantity");
            if($quantity != array_unique($quantity)){
                $this->messageManager->addErrorMessage("Duplicate quantity in Quantity Mapping");
            }
            else{
                foreach ($mappings as $mapping) {
                    $oldMap = $this->_mappingFactory->create()->getCollection()->addFieldToFilter("product_id",$productId)
                        ->addFieldToFilter("quantity",$mapping["quantity"])->getFirstItem();
                    $data["product_id"] = $productId;
                    $data["value"] = $mapping["value"];
                    $data["quantity"] = $mapping["quantity"];
                    if($oldMap->getData() == []){
                        $map = $this->_mappingFactory->create();
                        $map->setData($data);
                        $map->save();
                    }
                    else{
                        $map = $this->_mappingFactory->create();
                        $map->load($oldMap->getId());
                        $map->setValue($data["value"]);
                        $map->save();
                    }
                }
                //delete mapping
                $oldMappings = $this->_mappingFactory->create()->getCollection()->addFieldToFilter("product_id",$productId)->getData();
                foreach ($oldMappings as $key1 => $oldMapping){
                    foreach ($mappings as $key2 => $mapping){
                        if($oldMapping["quantity"] == $mapping["quantity"] && $oldMapping["value"] == $mapping["value"]){
                            unset($oldMappings[$key1]);
                            unset($mappings[$key2]);
                        }
                    }
                }
                if($oldMappings != []){
                    $this->_mappingFactory->create()->getCollection()->addFieldToFilter("id",["in" => array_column($oldMappings,"id")])->walk("delete");
                }
            }
        }
    }//end execute()
}//end class
