<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 13/08/2017
 * Time: 14:50
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class MassAssign  extends \Magento\Backend\App\Action
{

    protected $_session;
    protected $_productFactory;
    protected $_templateFactory;
    protected $_helperAssign;

    public function __construct(
        Action\Context $context,
        \Magenest\AdvancedProductOption\Model\ProductFactory $productFactory,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory,
        \Magenest\AdvancedProductOption\Helper\Assign $helperAssign
    )
    {
        $this->_session = $context->getSession();
        parent::__construct($context);
        $this->_productFactory = $productFactory;
        $this->_templateFactory = $templateFactory;
        $this->_helperAssign = $helperAssign;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {

        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        $excludeMode = $this->getRequest()->getParam('excludeMode');
        $template = $this->getRequest()->getParam('template');
        if ($excludeMode == 'true') {
            $this->_session->setExcludeMode(1);

        } else {
            $this->_session->setExcludeMode(0);
        }

        if (!empty($selected)) {
            $this->_session->setSelected(serialize($selected));
        } else {
            $selected = [0];
            $this->_session->setSelected(serialize($selected));
        }

        if (!empty($excluded)) {
            $this->_session->setExcluded(serialize($excluded));
        } else {
            $excluded = [0];
            $this->_session->setExcluded(serialize($excluded));
        }
    }
}