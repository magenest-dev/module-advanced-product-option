<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 02/08/2016
 * Time: 09:17
 */

namespace Magenest\AdvancedProductOption\Controller\Adminhtml\Template;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class Upload extends \Magento\Backend\App\Action
{

    protected $uploader;

    protected $directory;

    protected $filesystem;

    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context             $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\UploaderFactory  $uploader,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->uploader = $uploader;
        parent::__construct($context);
    }//end __construct()


    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_AdvancedProductOption::template');
    }//end _isAllowed()


    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $filesArray = $this->getRequest()->getFiles()->getArrayCopy();
            foreach ($filesArray as $key => $value) {
                if ($value['tmp_name'] != null) {
                    $fileId = $key;
                    $uploader = $this->uploader->create(['fileId' => $fileId]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $apo_path = 'apo/';

                    $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($apo_path));
                    $this->_eventManager->dispatch(
                        'aop_attach_upload_after',
                        [
                            'result' => $result,
                            'action' => $this,
                        ]
                    );
                    $anaName     = explode('/', $result['file']);
                    $desireIndex = (count($anaName) - 1);

                    $result['label'] = $anaName[$desireIndex];
                    unset($result['tmp_name']);
                    unset($result['path']);

                    $result['url']  = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getMediaUrl($result['file']);
                    $imageUrl = str_replace('catalog/product' , 'apo' , $result['url']);
                    $result['url'] = $imageUrl;
                    $result['file'] = $result['file'].'.tmp';
                }
            }

        } catch (\Exception $e) {
            $result = [
                       'error'     => $e->getMessage(),
                       'errorcode' => $e->getCode(),
                      ];
        }//end try

        /*
            * @var \Magento\Framework\Controller\Result\Raw $response
        */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }//end execute()
}//end class
