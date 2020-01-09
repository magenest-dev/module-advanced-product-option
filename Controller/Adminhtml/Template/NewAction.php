<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/07/2016
 * Time: 14:11
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class NewAction
 * @package Magenest\AdvancedProductOption\Controller\Adminhtml\Template
 */
class NewAction extends \Magento\Backend\App\Action
{

    protected $_resultForwardFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
