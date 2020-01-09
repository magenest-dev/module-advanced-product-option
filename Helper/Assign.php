<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/08/2016
 * Time: 14:26
 */

namespace Magenest\AdvancedProductOption\Helper;

class Assign extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $productFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magenest\AdvancedProductOption\Model\ProductFactory $productFactory
    ) {
        $this->productFactory = $productFactory;

        parent::__construct($context);
    }//end __construct()


    /**
     * @param $template_id
     * @param $product_ids
     */
    public function assignProduct($template_id, $product_ids)
    {
        $odAssignedProductIds = [];
        $productIdsA          = [];
        $oldAssigns           = $this->productFactory->create()->getCollection()->addFieldToFilter('template_id', $template_id);

        if ($oldAssigns->getSize() > 0) {
            foreach ($oldAssigns as $assign) {
                $odAssignedProductIds[] = $assign->getProductId();
            }
        }
        if(is_array($product_ids) || !empty($product_ids)){
            $productIdsA  = $product_ids;
        }else{
            $productIdsA = [];
        }

        $adds    = array_diff($productIdsA, $odAssignedProductIds);
        $deletes = array_diff($odAssignedProductIds, $productIdsA);

        // create  new assign product
        if ($adds) {
            foreach ($adds as $productId) {
                //whether the assignBean exist
                $exist = $this->isAssignObjectExist($productId,$template_id);

                if (!$exist) {
                    $assignBean = $this->productFactory->create()->setData(['product_id' => $productId, 'template_id' => $template_id])->save();
                }
            }
        }

        // delete the old assign product
        if ($deletes) {
            foreach ($deletes as $deletedProductId) {
                //whether the assignBean exist
                $exist = $this->isAssignObjectExist($deletedProductId,$template_id);
                if ($exist) {
                    $this->productFactory->create()->getCollection()->addFieldToFilter('template_id', array('eq' => $template_id))
                    ->addFieldToFilter('product_id', array('eq' => $deletedProductId))->getFirstItem()->delete();
                }
            }
        }
    }//end assignProduct()

    protected function isAssignObjectExist($product_id,$template_id) {
        $collection = $this->productFactory->create()->getCollection()->addFieldToFilter('template_id', $template_id) ->addFieldToFilter('product_id' ,$product_id);

        if ($collection->getSize() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param $product_id
     * @return mixed
     */
    public function getTemplate($product_id)
    {
        $result = [];
        $assignedCollections = $this->productFactory->create()->getCollection()->addFieldToFilter('product_id', $product_id)->getItems();
        foreach ($assignedCollections as $assignedCollection) {
            if ($assignedCollection->getId()) {
                $result[] = $assignedCollection->getTemplateId();
            }
        }
        return $result;
    }//end getTemplate()

    public function getAssignedProductByTemplateId($template_id)
    {
        $output =[];
        $collection = $this->productFactory->create()->getCollection()->addFieldToFilter('template_id', $template_id) ;

        if ($collection->getSize() > 0) {
            foreach ($collection as $item)
            {
                $output[] =  "'" .$item->getProductId() . "'";
            }
        }
        return $output;
    }
}//end class
