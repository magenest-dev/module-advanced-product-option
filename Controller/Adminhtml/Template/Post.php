<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/07/2016
 * Time: 21:32
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\App\ResponseInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Post
 * @package Magenest\AdvancedProductOption\Controller\Adminhtml\Template
 */
class Post extends \Magento\Backend\App\Action
{

    protected $templateFactory;

    protected $optionFactory;

    protected $rowFactory;

    protected $tierFactory;

    protected $assignHelper;

    protected $_productFactory;

    protected $_session;

    public function __construct(
        Context $context,
        \Magenest\AdvancedProductOption\Model\ProductFactory $productFactory,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory,
        \Magenest\AdvancedProductOption\Model\OptionFactory $optionFactory,
        \Magenest\AdvancedProductOption\Model\RowFactory $rowFactory,
        \Magenest\AdvancedProductOption\Model\TierPriceFactory $tierFactory,
        \Magenest\AdvancedProductOption\Helper\Assign $assignHelper,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    )
    {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
        $this->_productFactory = $productFactory;
        $this->templateFactory = $templateFactory;
        $this->optionFactory = $optionFactory;
        $this->rowFactory = $rowFactory;
        $this->tierFactory = $tierFactory;
        $this->assignHelper = $assignHelper;
        $this->_session = $context->getSession();
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $title = $this->getRequest()->getParam('title');
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        //Check Duplicate Title
        $checkDuplicateTitle = $this->templateFactory->create()->getCollection()->addFieldToFilter("title", $title)->getLastItem();
        if ($id) {
            if ($id != $checkDuplicateTitle->getData("template_id")) {
                $this->messageManager->addError("Duplicate title template.");
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        } else {
            if ($checkDuplicateTitle->getData("template_id") != null) {
                $this->messageManager->addErrorMessage(__('Title template is exist.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        $excludeMode = $this->_session->getExcludeMode();
        $selected = $this->_session->getSelected();
        //process excluded product
        $excluded = $this->_session->getExcluded();
        $optionIdsNeedCorrect = [];
        $rowIdsNeedCorrect = [];
        if ($id) {
            $isNewTemplateMode = false;
        } else {
            $isNewTemplateMode = true;
        }
        $templateBean = $this->templateFactory->create();
        if ($isNewTemplateMode) {
            $templateBean->setData(['title' => $title])->save();
        } else {
            $templateBean->load($id)->addData(['title' => $title])->save();
        }
        $optionsParams = $this->getRequest()->getParam('option');
        $rowsParams = $this->getRequest()->getParam('row');
        $tiersParams = $this->getRequest()->getParam('tier');
        $newOptionIds = [];
        $optionModel = $this->optionFactory->create();
        // row model
        $rowBean = $this->rowFactory->create();
        //Tier model
        $tierBean = $this->tierFactory->create();
        $oldOptions = $optionModel->getCollection()->addFieldToFilter('template_id', $templateBean->getId());
        $oldOptionIds = [];
        $oldRowIds = [];
        $oldeTierIds = [];
        if ($oldOptions->getSize() > 0) {
            foreach ($oldOptions as $option) {
                $oldOptionIds[] = $option->getOptionId();
            }
        }
        if ($optionsParams) {

            //Kiem tra trung title
            $titleOptions = [];
            foreach ($optionsParams as $optionsParam) {
                $titleOptions[] = $optionsParam["title"];
            }
            if (count($optionsParams) != count(array_unique($titleOptions))) {
                $this->messageManager->addError("Title option is exist.");
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }

            foreach ($optionsParams as $optionsParam) {
                $optionTitle = $optionModel->getCollection()->addFieldToFilter("title", $optionsParam["title"])
                    ->addFieldToFilter("template_id", $id)->getLastItem();
                if ($optionTitle->getData("option_id") != null && $optionTitle->getData("option_id") != $optionsParam["id"]) {
                    $this->messageManager->addError("Duplicate title option.");
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }

            }

            ///////////////////////////////////

            foreach ($optionsParams as $option) {
                $isNewMode = $option['is_new'];
                $optionDataTooltip = $option['tooltip'];

                $optionDataTitle = $option['title'];
                if (isset($option['is_required'])) {
                    $optionDataIsRequire = $option['is_required'];
                } else {
                    $optionDataIsRequire = 0;
                }
                if ($isNewMode === '1' || $isNewMode === 'true') {
                    $optionBean = $this->optionFactory->create();
                    $optionBean->setData([
                        'template_id' => $templateBean->getId(),
                        'type' => $option['type'],
                        'title' => $optionDataTitle,
                        'excludeMode' => $excludeMode,
                        'excluded' => $excluded,
                        'tooltip' => $optionDataTooltip,
                        'is_required' => $optionDataIsRequire
                    ])->save();
                    $optionIdsNeedCorrect[$option['id']] = $optionBean->getId();
                } else {
                    $newOptionIds[] = $option['id'];
                    $optionBean = $optionModel->load($option['id'])
                        ->addData(['title' => $optionDataTitle,
                            'is_required' => $optionDataIsRequire,
                            'excludeMode' => $excludeMode,
                            'tooltip' => $optionDataTooltip,
                            'type' => $option['type']
                        ]);
                    if ($excludeMode != null) $optionBean->addData(['excludeMode' => $excludeMode]);
                    if ($excluded != null) $optionBean->addData(['excluded' => $excluded]);
                    $optionBean->save();
                    $oldRows = $rowBean->getCollection()->addFieldToFilter('option_id', $option['id']);
                    if ($oldRows->getSize() > 0) {
                        foreach ($oldRows as $row) {
                            $oldRowIds[] = $row->getRowId();
                        }
                    }
                }
            }
        }

        // Delete Options
        $deletes = array_diff($oldOptionIds, $newOptionIds);
        if ($deletes) {
            foreach ($deletes as $deletedOptionId) {
                $deleteRowByOptions = $rowBean->getCollection()->addFieldToFilter('option_id', $deletedOptionId);
                foreach ($deleteRowByOptions as $deleteRowByOption) {
                    $tierBean->getCollection()->addFieldToFilter('row_id', $deleteRowByOption["row_id"])->walk('delete');
                    $this->rowFactory->create()->load($deleteRowByOption["row_id"])->delete();
                }
                $optionModel->load($deletedOptionId)->delete();
            }
        }

        $newRowIds = [];
        //process Row Data isset($var) ? $var : "default";
        if ($rowsParams) {

            //kiem tra title row
            foreach ($rowsParams as $rowsParam) {
                foreach ($rowsParams as $row) {
                    if ($rowsParam["title"] == $row["title"] && $rowsParam["option_id"] == $row["option_id"]
                        && $rowsParam["id"] != $row["id"]
                    ) {
                        $this->messageManager->addError("Title row is exist.");
                        return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                    }
                }
            }

            foreach ($rowsParams as $rowsParam) {
                $rowTitle = $rowBean->getCollection()->addFieldToFilter("title", $rowsParam["title"])
                    ->addFieldToFilter("option_id", $rowsParam["option_id"])->getLastItem();
                if ($rowTitle->getData("row_id") != null && $rowTitle->getData("row_id") != $rowsParam["id"]) {
                    $this->messageManager->addError("Duplicate title row.");
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }

            }

            /////////////////////////////////

            foreach ($rowsParams as $row) {
                $isNewModeForRow = $row['is_new'];
                $specialPrice = 0;
                if (isset($row['price_type'])) {

                }
                if ($isNewModeForRow === "1" || $isNewModeForRow === "true") {
                    //is it need to correct the option_id
                    $option_id = $row['option_id'];
                    if (in_array($option_id, array_keys($optionIdsNeedCorrect))) {
                        $option_id = $optionIdsNeedCorrect[$row['option_id']];
                    }
                    $childrenRow = isset($row['children']) ? $row['children'] : "";
                    if (is_array($childrenRow)) {
                        $childrenRow = implode(',', $childrenRow);
                    }
                    $rowBean->setData([
                        'option_id' => $option_id,
                        'description' => isset($row['description']) ? $row['description'] : "",
                        'tooltip' => isset($row['tooltip']) ? $row['tooltip'] : "",
                        'enableTooltip' => isset($row['enableTooltip']) ? true : false,
                        'title' => isset($row['title']) ? $row['title'] : "",
                        'sku' => isset($row['sku']) ? $row['sku'] : "",
                        'qty' => isset($row['qty']) ? $row['qty'] : "",

                        'image' => isset($row['image']) ? $row['image'] : "",
                        'swatch' => isset($row['swatch']) ? $row['swatch'] : "",
                        'productImg' => isset($row['productImg']) ? $row['productImg'] : "",

                        'children' => $childrenRow,
                        'price' => isset($row['price']) ? $row['price'] : "0",
                        'price_type' => isset($row['price_type']) ? $row['price_type'] : "0",
                        'special_price' => isset($row['special_price']) ? $row['special_price'] : "0",
                        'calculate_type' => isset($row['calculate_type']) ? $row['calculate_type'] : "addition",
                        'special_date_from' => isset($row['special_date_from']) ? $row['special_date_from'] : '',
                        'special_date_to' => isset($row['special_date_to']) ? $row['special_date_to'] : ''
                    ])->save();
                    $rowIdsNeedCorrect[$row['id']] = $rowBean->getId();
                } else {
                    $newRowIds[] = $row['id'];
                    $rowBean->load($row['id']);
                    $childrenRow = isset($row['children']) ? $row['children'] : "";
                    if (is_array($childrenRow)) {
                        $childrenRow = implode(',', $childrenRow);
                    }
                    $rowBean->addData([
                        'description' => isset($row['description']) ? $row['description'] : "",
                        'title' => isset($row['title']) ? $row['title'] : "",
                        'tooltip' => isset($row['tooltip']) ? $row['tooltip'] : "",
                        'enableTooltip' => isset($row['enableTooltip']) ? true : false,
                        'sku' => isset($row['sku']) ? $row['sku'] : "",
                        'qty' => isset($row['qty']) ? $row['qty'] : "",
                        'image' => isset($row['image']) ? $row['image'] : "",
                        'swatch' => isset($row['swatch']) ? $row['swatch'] : "",
                        'productImg' => isset($row['productImg']) ? $row['productImg'] : "",
                        'children' => $childrenRow,
                        'price' => isset($row['price']) ? $row['price'] : "0",
                        'price_type' => isset($row['price_type']) ? $row['price_type'] : "0",
                        'special_price' => isset($row['special_price']) ? $row['special_price'] : "0",
                        'calculate_type' => isset($row['calculate_type']) ? $row['calculate_type'] : "addition",
                        'special_date_from' => isset($row['special_date_from']) ? $row['special_date_from'] : '',
                        'special_date_to' => isset($row['special_date_to']) ? $row['special_date_to'] : ''
                    ])->save();

                    $oldTiers = $tierBean->getCollection()->addFieldToFilter('row_id', $row['id']);
                    if ($oldTiers->getSize() > 0) {
                        foreach ($oldTiers as $tier) {
                            $oldeTierIds[] = $tier->getId();
                        }
                    }
                }
            }
        }
        //Delete Rows
        $deleteRows = array_diff($oldRowIds, $newRowIds);
        if ($deleteRows) {
            foreach ($deleteRows as $deleteRow) {
                $tierBean->getCollection()->addFieldToFilter('row_id', $deleteRow)->walk('delete');
                $rowBean->load($deleteRow)->delete();
            }
        }
        //process the tier Data
        $newTierIds = [];
        if ($tiersParams) {

//            //kiem tra min max tier
            $tiersChecks = $tiersParams;
            foreach ($tiersChecks as $tiersParamKey => $tiersParamVal) {
                $checkTier = [];
                foreach ($tiersChecks as $tierKey => $tierVal) {
                    if ($tiersParamVal["row_id"] == $tierVal["row_id"]) {
                        $checkTier[] = $tierVal;
                        unset($tiersChecks[$tierKey]);
                    }
                }
                for ($j = 0; $j < count($checkTier) - 1; $j++) {
                    for ($z = $j + 1; $z < count($checkTier); $z++) {
                        if (
                            $checkTier[$j]['min_qty'] > $checkTier[$j]['max_qty'] ||
                            $checkTier[$z]['min_qty'] > $checkTier[$z]['max_qty'] ||
                            ($checkTier[$j]['min_qty'] <= $checkTier[$z]['max_qty'] &&
                                $checkTier[$j]['max_qty'] >= $checkTier[$z]['max_qty']) ||
                            ($checkTier[$j]['min_qty'] <= $checkTier[$z]['min_qty'] &&
                                $checkTier[$j]['max_qty'] >= $checkTier[$z]['min_qty']) ||
                            ($checkTier[$j]['min_qty'] >= $checkTier[$z]['max_qty'] &&
                                $checkTier[$j]['max_qty'] <= $checkTier[$z]['max_qty']) ||
                            ($checkTier[$j]['min_qty'] >= $checkTier[$z]['min_qty'] &&
                                $checkTier[$j]['max_qty'] <= $checkTier[$z]['min_qty'])
                        ) {
                            $this->messageManager->addError("Min or Max qty in tier error.");
                            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                        }
                    }
                }

            }
            /////////////////////////////////

            foreach ($tiersParams as $tier) {
                if ($tier['is_new'] === '1' || $tier['is_new'] === "true") {
                    $isNewModeForTier = true;
                    //is it need to correct the row_id
                    if (in_array($tier['row_id'], array_keys($rowIdsNeedCorrect))) {
                        $row_id = $rowIdsNeedCorrect[$tier['row_id']];
                    } else {
                        $row_id = $tier['row_id'];
                    }
                    $tierData = ['row_id' => $row_id,
                        'website_id' => $tier['website_id'],
                        'customer_group' => $tier['customer_group'],
                        'price' => isset($tier['price']) ? $tier['price'] : "",
                        'min_qty' => isset($tier['min_qty']) ? $tier['min_qty'] : "",
                        'max_qty' => isset($tier['max_qty']) ? $tier['max_qty'] : "",
                        'tier_price_type' => isset($tier['tier_price_type']) ? $tier['tier_price_type'] : "fixed",
                        'calculate_tier_type' => isset($tier['calculate_tier_type']) ? $tier['calculate_tier_type'] : "addition"
                    ];

                    $tierBean->setData($tierData)->save();
                } else {
                    $isNewModeForTier = false;
                    $newTierIds[] = $tier['id'];
                    if (in_array($tier['row_id'], array_keys($rowIdsNeedCorrect))) {
                        $row_id = $rowIdsNeedCorrect[$tier['row_id']];
                    } else {
                        $row_id = $tier['row_id'];
                    }
                    $tierBean->load($tier['id']);
                    $tierData = [
                        'row_id' => $row_id,
                        'website_id' => isset($tier['website_id']) ? $tier['website_id'] : 0,
                        'customer_group' => isset($tier['customer_group']) ? $tier['customer_group'] : 0,
                        'price' => isset($tier['price']) ? $tier['price'] : "",
                        'min_qty' => isset($tier['min_qty']) ? $tier['min_qty'] : "",
                        'max_qty' => isset($tier['max_qty']) ? $tier['max_qty'] : "",
                        'tier_price_type' => isset($tier['tier_price_type']) ? $tier['tier_price_type'] : "fixed",
                        'calculate_tier_type' => isset($tier['calculate_tier_type']) ? $tier['calculate_tier_type'] : "addition"
                    ];
                    $tierBean->addData($tierData)->save();
                }
            }
        }
        //Delete Tier
        $deleteTiers = array_diff($oldeTierIds, $newTierIds);
        if ($deleteTiers) {
            foreach ($deleteTiers as $deleteTier) {
                $tierBean->load($deleteTier)->delete();
            }
        }
        // save selected products
        if ($excludeMode == 0 && $selected != false) {
            $this->assignProduct($templateBean->getId());
        } elseif ($excludeMode == 1 && $selected != false) {
            $ids = $this->_productFactory->create()->getListIdProductOut($id);
            $productCollecction = $this->_objectManager->create("\Magento\Catalog\Model\ResourceModel\Product\Collection");
            $productIds = $productCollecction->addFieldToFilter("entity_id", ["nin" => $ids])->getItems();
            $ids = [];
            foreach ($productIds as $productId) {
                $ids[] = $productId->getEntityId();
            }
            $this->_session->setSelected(serialize($ids));
            $this->assignProduct($templateBean->getId());
        }
        $this->_session->clearStorage();
        if($this->getRequest()->getParam("saveandcontinueedit") == "true" && $id != null){
            $this->messageManager->addSuccess("Template has been saved.");
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }else{
            $this->messageManager->addSuccess(__('Template has been saved.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }

    private function assignProduct($template_id)
    {
        $selected = unserialize($this->_session->getSelected());
        $product_ids = $selected;
        $excluded = unserialize($this->_session->getExcluded());
        if (!empty($excluded)) {
            $product_ids = array_diff($selected, $excluded);
        }
        $this->assignHelper->assignProduct($template_id, $product_ids);
        $this->_session->clearStorage();
    }

    private function saveOptions($template_id, $optionsParams, $excludeMode, $excluded)
    {
        $oldOptionIds = [];
        $newOptionIds = [];
        $oldOptions = $this->optionFactory->create()->getCollection()->addFieldToFilter('template_id', $template_id);
        if ($oldOptions->getSize() > 0) {
            foreach ($oldOptions as $option) {
                $oldOptionIds[] = $option->getOptionId();
            }
        }
        foreach ($optionsParams as $option) {
            $isNewMode = $option['is_new'];
            $optionDataTooltip = $option['tooltip'];

            $optionDataTitle = $option['title'];
            if (isset($option['is_required'])) {
                $optionDataIsRequire = $option['is_required'];
            } else {
                $optionDataIsRequire = 0;
            }

            if ($isNewMode === '1' || $isNewMode === 'true') {
                $optionBean = $this->optionFactory->create();
                $optionBean->setData([
                    'template_id' => $template_id,
                    'type' => $option['type'],
                    'title' => $optionDataTitle,
                    'excludeMode' => $excludeMode,
                    'excluded' => $excluded,
                    'tooltip' => $optionDataTooltip,
                    'is_required' => $optionDataIsRequire
                ])->save();
                $optionIdsNeedCorrect[$option['id']] = $optionBean->getId();
            } else {
                $newOptionIds = $option['id'];
                $optionBean = $this->optionFactory->create()->load($option['id'])
                    ->addData(['title' => $optionDataTitle,
                        'is_required' => $optionDataIsRequire,
                        'excludeMode' => $excludeMode,
                        'tooltip' => $optionDataTooltip,
                        'type' => $option['type']
                    ]);

                if ($excludeMode != null) $optionBean->addData(['excludeMode' => $excludeMode]);
                if ($excluded != null) $optionBean->addData(['excluded' => $excluded]);
                $optionBean->save();
            }
        }
        $deletes = array_diff($oldOptionIds, $newOptionIds);
        if ($deletes) {
            foreach ($deletes as $deletedOptionId) {
                $this->optionFactory->create()->load($deletedOptionId)->delete();
            }
        }
    }
}
