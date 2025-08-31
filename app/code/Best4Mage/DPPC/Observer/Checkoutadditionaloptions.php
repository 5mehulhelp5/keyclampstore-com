<?php

namespace Best4Mage\DPPC\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
 
class Checkoutadditionaloptions implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Best4Mage\DPPC\Block\Product\View
     */
    protected $dppcBlock;
    /**
     * @var \Best4Mage\DPPC\Helper\Data
     */
    protected $dppcHelper;
    /**
     * @var \Best4Mage\DPPC\Model\FormulaInterpreter\Compiler
     */
    protected $compiler;
    /**
     * @var \Best4Mage\DPPC\Model\Unit
     */
    protected $unit;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Best4Mage\DPPC\Block\Product\View $dppcBlock
     * @param \Best4Mage\DPPC\Helper\Data $dppcHelper
     * @param \Best4Mage\DPPC\Model\FormulaInterpreter\Compiler $compiler
     * @param \Best4Mage\DPPC\Model\Unit $unit
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Best4Mage\DPPC\Block\Product\View $dppcBlock,
        \Best4Mage\DPPC\Helper\Data $dppcHelper,
        \Best4Mage\DPPC\Model\FormulaInterpreter\Compiler $compiler,
        \Best4Mage\DPPC\Model\Unit $unit,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->dppcBlock = $dppcBlock;
        $this->dppcHelper = $dppcHelper;
        $this->compiler = $compiler;
        $this->unit = $unit;
        $this->productMetadata = $productMetadata;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->dppcHelper->isEnable()) {
            /* @var \Magento\Quote\Model\Quote\Item $item */
            
            $item = $observer->getQuoteItem();
            
            $product = $item->getProduct();

            $sideOption = [];

            $productId = $product->getId();

            $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());

            $totalCustomOption = $optionTotalPrice = $totalDeduction = 0 ;
                                
            if ($product->getDppcProductEnable()) {
                $sideOptionParams = $this->request->getPost('side_options');
                $selectedShapeId = $this->request->getPost('selected_shape');
                $selectedShapeFormula = trim($this->request->getPost('selected_shape_formula'));
                $dppcTotal = $this->request->getPost('dppc_price');
                $customOption = $item->getBuyRequest();
                
                try {
                    if (count($sideOptionParams) > 0 && $sideOptionParams != null && $selectedShapeId != null) {
                        $shapeTitle  = $this->dppcBlock->getShapeById($selectedShapeId);

                        $shapeDetails['Choose a Shape'] = $shapeTitle;

                        $sideOptions = $sideOptionParams[$productId][$selectedShapeId];

                        $sideOptions = array_merge($shapeDetails, $sideOptions);
                       
                        $additionalOptions = [];

                        if ($additionalOption = $item->getOptionByCode('additional_options')) {
                            //if (version_compare($this->getMagentoVersion(), '2.2.0', '>=')) {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                $serializer = $objectManager->create('Magento\Framework\Serialize\Serializer\Json');
                                $additionalOptions = array_merge($additionalOption, $serializer->unserialize($additionalOption->getValue()));
                            // } else {
                            //     $additionalOptions = array_merge($additionalOption, unserialize($additionalOption->getValue()));
                            // }
                        }
                        
                        if ($customOption['options']) {
                            $optionIds = [];
                            foreach ($customOption['options'] as $key => $op) {
                                if (is_array($op)) {
                                    $count = 1;
                                    foreach ($op as $v => $value) {
                                        unset($op['day_part']);
                                        if (is_numeric($v)) {
                                            $optionIds[] =  $value;
                                        } else {
                                            if (!($value == "" || $v == 'day_part')) {
                                                if ($count == 1) {
                                                    $optionIds[] =  $key;
                                                    $count++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $optionIds[] =  $key;
                                }
                            }
                            if ($this->dppcHelper->configurePrice()) {
                                foreach ($product->getOptions() as $key => $option) {
                                    if ($option->getValues() > 0) {
                                        foreach ($option->getValues() as $value) {
                                            if ($value->getData('price_type') == 'percent') {
                                                foreach ($optionIds as $id) {
                                                    if ($id == $value->getData('option_type_id')) {
                                                        $percent = $value->getData('price');
                                                        $deductAmt = $product->getPrice() * ($percent/100);
                                                        $counttotal = ($dppcTotal*$percent)/100;
                                                        $optionTotalPrice += $counttotal;
                                                        $totalDeduction += $deductAmt;
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        if ($option->getData('price_type') == 'percent') {
                                            foreach ($optionIds as $id) {
                                                if ($id == $option->getData('option_id')) {
                                                    $percent = $option->getData('price');
                                                    $deductAmt = $product->getPrice() * ($percent/100);
                                                    $counttotal = ($dppcTotal*$percent)/100;
                                                    $optionTotalPrice += $counttotal;
                                                    $totalDeduction += $deductAmt;
                                                }
                                            }
                                        }
                                    }
                                }
                            }else{
                                foreach ($product->getOptions() as $key => $option) {
                                    if ($option->getValues() > 0) {
                                        foreach ($option->getValues() as $value) {
                                            foreach ($optionIds as $id) {
                                                if ($id == $value->getData('option_type_id')) {
                                                    $price = $value->getData('price');
                                                    $optionTotalPrice += $price;
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($optionIds as $id) {
                                            if ($id == $option->getData('option_id')) {
                                                $price = $option->getData('price');
                                                $optionTotalPrice += $price;
                                            }
                                        }
                                    }
                                }
                            }
                            $totalCustomOption = $optionTotalPrice - $totalDeduction ;
                        }
                        
                        if (is_array($sideOptions)) {
                            foreach ($sideOptions as $key => $value) {
                                $sideTitle = $key;

                                if ($key != 'Choose a Shape') {
                                    $sideTitle = $this->dppcBlock->getSideByCode($key);
                                }
                                
                                if ($key == '' || $value == '') {
                                    continue;
                                }
                                $additionalOptions[] = [
                                    'label' => $sideTitle,
                                    'value' => $value
                                ];
                            }
                        }

                        if (count($additionalOptions) > 0) {
                            //if (version_compare($this->getMagentoVersion(), '2.2.0', '>=')) {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                $serializer = $objectManager->create('Magento\Framework\Serialize\Serializer\Json');

                                $serializedOptions = $serializer->serialize($additionalOptions);
                            //} else {
                            //    $serializedOptions = serialize($additionalOptions);
                            //}

                            $item->addOption([
                                'product_id' => $productId,
                                'code' => 'additional_options',
                                'value' => $serializedOptions
                            ]);
                        }
                        
                        if ($selectedShapeFormula != null) {
                            $sideOptions = $sideOptionParams[$productId][$selectedShapeId];

                            $customProductPrice = $this->getShapePrice($product, $item, $sideOptions, $selectedShapeFormula);

                            if ($customProductPrice) {
                                $productQty = $product->getQty();
                                $finalPrice = $product->getFinalPrice($productQty);
                                
                                $customProductPrice = $customProductPrice + $finalPrice + $totalCustomOption;

                                $item->setCustomPrice($customProductPrice);
                                $item->setOriginalCustomPrice($customProductPrice);
                                $item->getProduct()->setIsSuperMode(true);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }
        }
    }

    public function getShapePrice($product, $item, $sideOptions, $selectedShapeFormula)
    {

        $product = $product->getId();

        $unit = $this->getUnitModel($item);

        $unitprice = (float) $unit->getDppcUnitPrice();

        try {
            if (count($sideOptions) > 0 && $sideOptions != null && $unitprice > 0) {
                $variable = [];

                foreach ($sideOptions as $key => $value) {
                    $variable[$key] = $unit->convertProductUnit((float) $value);
                }

                $area = 0;

                $executable = $this->compiler->compile($selectedShapeFormula);

                $constants = [
                   'PI' => 3.141592653589793
                ];

                $variables = array_merge($variable, $constants);

                $area = $executable->run($variables);

                return $area * $unitprice;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }



        return false;
    }

    public function getUnitModel($item)
    {
        
        $_unitModel = $this->unit;

        $_product = $item->getProduct();

        if ($_product->getId()) {
            if ($_product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $_product->load($_product->getId());
                 
                foreach ($item->getChildren() as $child) {
                    $_unitModel->setProduct($child->getProduct())->setParentProduct($_product);
                     
                    return $_unitModel;
                }
            }
             
            $_unitModel->setProduct($_product);
        }
        
        return $_unitModel;
    }

    public function getMagentoVersion()
    {
        if (defined('AppInterface::VERSION')) {
            return AppInterface::VERSION;
        } else {
            return $this->productMetadata->getVersion();
        }
    }
}
