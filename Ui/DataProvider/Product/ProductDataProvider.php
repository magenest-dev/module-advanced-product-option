<?php

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 12/08/2017
 * Time: 23:22
 */
namespace  Magenest\AdvancedProductOption\Ui\DataProvider\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
//class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;
    
    /**
     * Product collection
     *
     * @var \Magenest\AdvancedProductOption\Model\Product
     */
    protected $productTemplate;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    protected $coreRegistry;

    protected $session;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magenest\AdvancedProductOption\Model\Product $productTemplate,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManagerInterface $sessionManagerInterface,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->productTemplate = $productTemplate;
        $this->coreRegistry = $registry;
        $this->session = $sessionManagerInterface;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            if($this->coreRegistry->registry('template_id')){
                $templateId = $this->coreRegistry->registry('template_id');
                $ids = $this->productTemplate->getListIdProductOut($templateId);
                $this->getCollection()->addFieldToFilter("entity_id",["nin" => $ids])->load();
                $this->session->start();
                $this->session->setTemplateId($this->coreRegistry->registry('template_id'));
            }
            else{
                $this->session->start();
                $templateId =  $this->session->getTemplateId();
                $ids = $this->productTemplate->getListIdProductOut($templateId);
                $this->getCollection()->addFieldToFilter("entity_id",["nin" => $ids])->load();
            }

        }
        $items = $this->getCollection()->toArray();

        foreach ($items as &$stuff) {
            $stuff['ids'] = ["'".$stuff['entity_id']."'"];
        }

        $input = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items)
        ];
        return $input;
    }

    public function getIds() {
        return ['195','189'];
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}