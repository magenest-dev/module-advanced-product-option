<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 13/05/2016
 * Time: 23:39
 */
namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{


    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magenest_AdvancedProductOption::advancedproductoption');
        $resultPage->addBreadcrumb(__('Template'), __('Template'));
        $resultPage->addBreadcrumb(__('Manage Template'), __('Manage Template'));
        $resultPage->getConfig()->getTitle()->prepend(__('Template'));
        return $resultPage;
    }//end execute()
}//end class
