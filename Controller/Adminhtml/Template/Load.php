<?php
/**
 * Load the option template detail for editing
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 16/05/2016
 * Time: 01:10
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;

class Load extends \Magento\Backend\App\Action
{
    protected $templateFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory
    ){
        parent::__construct($context);
        $this->templateFactory = $templateFactory;
    }//end __construct()
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute(){
        if($this->_request->getParam("clearSession") == true){
            $this->_session->unsTemplateId();
            return;
        }
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $options = $this->getOption($id);
            $a = $result->setData($options);
            return $result->setData($options);
        } else {
            $templateData = [];
            return $result->setData($templateData);
        }
    }//end execute()
    private function getOption($template_id){
        /** @var \Magenest\AdvancedProductOption\Model\Template $template */
        $template = $this->templateFactory->create()->load($template_id);
        $options = $template->getOptionsWithFullInfo();
        return $options;
    }//end getOption()
}//end class
