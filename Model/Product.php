<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/08/2016
 * Time: 13:36
 */

namespace Magenest\AdvancedProductOption\Model;

class Product extends \Magento\Framework\Model\AbstractModel
{


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\AdvancedProductOption\Model\ResourceModel\Product');
    }//end _construct()

    public function getListIdProductOut($template_id){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tblSalesOrder = $connection->getTableName('magenest_apo_product');
        $results = [];
        if($template_id == null){
            $results = $connection->fetchAll('SELECT product_id FROM `'.$tblSalesOrder.'`');
        }
        else{
            $results = $connection->fetchAll('SELECT product_id FROM `'.$tblSalesOrder.'` WHERE template_id !='.$template_id);
        }
        $ids = [];
        foreach ($results as $result){
            $ids[] = $result["product_id"];
        }
        if(count($ids) == 0){
            return null;
        }
        return $ids;
    }
}//end class
