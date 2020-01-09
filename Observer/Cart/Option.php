<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 09/08/2016
 * Time: 14:00
 */
namespace Magenest\AdvancedProductOption\Observer\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class Option implements ObserverInterface
{
    /**
     * @var \Magenest\AdvancedProductOption\Model\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magenest\AdvancedProductOption\Model\RowFactory
     */
    protected $rowFactory;

    /**
     * @var \Magenest\AdvancedProductOption\Model\TierPriceFactory
     */
    protected $tierFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    public function __construct(
        \Magenest\AdvancedProductOption\Model\OptionFactory $optionFactory,
        \Magenest\AdvancedProductOption\Model\RowFactory $rowFactoryFactory,
        \Magenest\AdvancedProductOption\Model\TierPriceFactory $tierPriceFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        $this->optionFactory = $optionFactory;
        $this->rowFactory = $rowFactoryFactory;
        $this->tierFactory = $tierPriceFactory;
        $this->_request = $requestInterface;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }//end __construct()

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getQuoteItem();
        $buyInfo = $item->getBuyRequest();
        /*
            @var  $product  \Magento\Catalog\Model\Product
        */
        $product = $observer->getEvent()->getProduct();
        // checkbox
        $advancedOptionParams = $this->_request->getParam("aop");

        $additionalOptions = [];

        if ($additionalOption = $item->getOptionByCode('additional_options')) {
            $additionalOptions = (array)unserialize($additionalOption->getValue());
        }

        //
        // intervene the buyRequest
        // $buyRequestItem = $item->getOptionByCode('info_buyRequest');
        // $aop=[2=>'ruppee', 9=>'extend_licens']
        // $buyRequestItemArr = unserialize($buyRequestItem->getData());
        // if (isset($buyRequestItemArr['aop'])) {
        // }
        //
        $productOption = [];

        /*
            * aop[1] = 2;
            * aop[4] ='regular';
         */
        if ($advancedOptionParams) {
            foreach ($advancedOptionParams as $aopId => $value) {
                if ($value) {
                    //$rowBean = $this->rowFactory->create()->load($value);
                    $optionBean = $this->optionFactory->create()->load($aopId);
                    if ($optionBean->getId()) {
                        $optionLabel = $optionBean->getTitle();
                        $optionValue = '';

                        // depend on the type of option such as text, file, textarea, select, checkbox
                        $optionType = $optionBean->getType();

                        if ($optionType === 'select' || $optionType === 'radio') {
                            $rowBean = $this->rowFactory->create()->load($value);
                            if ($rowBean->getId()) {
                                $optionValue = $rowBean->getTitle();
                            }

                            $additionalOptions[] = [
                                'label' => $optionLabel,
                                'value' => $optionValue,
                            ];
                            $productOption[] = [
                                'label' => $optionLabel,
                                'value' => $optionValue,
                            ];
                        } elseif ($optionType === 'text' || $optionType === 'textarea') {
                            $rowBean = $this->rowFactory->create()->load($value);
                            $additionalOptions[] = [
                                'label' => $optionLabel,
                                'value' => $value,
                            ];
                            $productOption[] = [
                                'label' => $optionLabel,
                                'value' => $value,
                            ];
                        } elseif ($optionType === 'checkbox') {
                            // the value of the checkbox is the id of the row
                            if ($value) {
                                if (!is_array($value)) {
                                    $options = explode(',', $value);
                                } else {
                                    $options = $value;
                                }
                                $optionValue = '';
                                if (count($options) > 0) {
                                    foreach ($options as $optionId) {
                                        $rowTitle = $this->rowFactory->create()->load($optionId);
                                        if ($rowTitle->getId()) {
                                            $optionLabel = $rowTitle->getTitle();
                                            $optionValue = 'yes';
                                            $additionalOptions[] = [
                                                'label' => $optionLabel,
                                                'value' => $optionValue,
                                            ];
                                            $productOption[] = [
                                                'label' => $optionLabel,
                                                'value' => $optionValue,
                                            ];
                                        }
                                    }
                                }
//                                $rowBean = $this->rowFactory->create()->load($value);
//                                $optionLabel = $rowBean->getTitle();
//                                $optionValue = 'yes';

                            }
                        } elseif ($optionType === 'swatch') {
//                            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
//                            $logger =  $objectManager->create('Magento\Framework\Logger\Monolog');
//                            $logger->debug($value);

                            if (!is_array($value)) {
                                $options = explode(',', $value);
                            } else {
                                $options = $value;
                            }
                            $optionValue = '';
                            if (count($options) > 0) {
                                foreach ($options as $optionId) {
                                    $rowTitle = $this->rowFactory->create()->load($optionId);
                                    if ($rowTitle->getId()) {
                                        $optionValue .= $rowTitle->getTitle() . '   ';
                                    }
                                }
                                ///////////////////
                                $additionalOptions[] = [
                                    'label' => $optionLabel,
                                    'value' => $optionValue,
                                ];
                                $productOption[] = [
                                    'label' => $optionLabel,
                                    'value' => $optionValue,
                                ];
                                ////////////////////
                            }
                        }//end if
                    }//end if

                    // $optionLabel = __($key) ;
                }//end if
            }//end foreach
        }//end if

        // only add option to item
        if (is_array($additionalOptions) && !empty($additionalOptions)) {

            //add the advanced product option
            $item->addOption(
                [
                    'code' => 'apo_options',
                    'value' => $this->serializer->serialize($additionalOptions),
                    'product_id' => $product->getId()
                ]
            );

            $item->addOption(
                [
                    'code' => 'additional_options',
                    'value' => $this->serializer->serialize($additionalOptions),
                    'product_id' => $product->getId()
                ]
            );

        }

        $optionsProduct = $product->getCustomOption('additional_options');

//        if (!$optionsProduct) {
//            $product->addCustomOption('additional_options', $this->serializer->serialize($productOption));
//        }

        // $product->addCustomOption('aop_19' ,'2');
    }//end execute()
}//end class
