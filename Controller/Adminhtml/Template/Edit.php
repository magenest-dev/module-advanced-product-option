<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/07/2016
 * Time: 13:50
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{

    protected $coreRegistry;

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }//end __construct()


    public function execute()
    {
        $template_id = $this->getRequest()->getParam('id');
        $this->coreRegistry->register('template_id', $template_id);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_AdvancedProductOption::advancedproductoption_template');
        $resultPage->getConfig()->getTitle()->set(__('Template'));
        return $resultPage;
    }//end execute()
}//end class
