<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/07/2016
 * Time: 20:25
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class WebsiteFeed extends \Magento\Backend\App\Action
{

    /**
     * @var \Magenest\AdvancedProductOption\Helper\Website
     */
    protected $websiteHelper;

    /**
     * @var  \Magenest\AdvancedProductOption\Helper\CustomerGroup
     */
    protected $customerGroupHelper;

    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\AdvancedProductOption\Helper\Website $websiteHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magenest\AdvancedProductOption\Helper\CustomerGroup $customerGroupHelper
    ) {
        parent::__construct($context);
        $this->customerGroupHelper = $customerGroupHelper;
        $this->websiteHelper       = $websiteHelper;
        $this->resultJsonFactory = $jsonFactory;
    }//end __construct()


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $out['website']       = $this->websiteHelper->getWebsites();
        $out['customergroup'] = $this->customerGroupHelper->getCustomerGroups();
        return $resultJson->setData($out);
    }//end execute()
}//end class
