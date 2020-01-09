<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 04/08/2016
 * Time: 10:00
 */
namespace Magenest\AdvancedProductOption\Controller\Template;

use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;

class Feed extends \Magento\Framework\App\Action\Action
{

    protected $templateFactory;

    protected $assignHelper;

    protected $resultJsonFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResultJsonFactory $resultFactory,
        \Magenest\AdvancedProductOption\Model\TemplateFactory $templateFactory,
        \Magenest\AdvancedProductOption\Helper\Assign $assignHelper
    ) {
        parent::__construct($context);
        $this->templateFactory = $templateFactory;
        $this->assignHelper    = $assignHelper;
        $this->resultJsonFactory  = $resultFactory;
    }//end __construct()

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $productId  = $this->getRequest()->getParam('product');
        $templateId = $this->assignHelper->getTemplate($productId);
        $template   = $this->templateFactory->create()->load($templateId);
        // $options = $template->getOptionsWithFullInfo();
        $options = $template->getOptionsWithFullInfoFrontend();

        $result = $this->resultJsonFactory->create();

        $result = $result->setData($options);

        return $result;
    }//end execute()
}//end class
