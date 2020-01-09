<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 13:23
 */
namespace Magenest\AdvancedProductOption\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class TemplateActions extends Column
{

    protected $urlBuilder;


    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }//end __construct()


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href'   => $this->urlBuilder->getUrl(
                    'advancedproductoption/template/edit',
                        [
                            'id'    => $item['template_id'],
                            'store' => $storeId,
                        ]
                    ),
                    'label'  => __('Edit'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }//end prepareDataSource()
}//end class
