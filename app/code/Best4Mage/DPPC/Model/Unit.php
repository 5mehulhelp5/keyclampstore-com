<?php
namespace Best4Mage\DPPC\Model;

class Unit extends \Magento\Framework\Model\AbstractModel
{

    const TYPE_MM = 'Millimeter';
    
    const TYPE_CM = 'Centimeter';
    
    const TYPE_M = 'Meter';
    
    const TYPE_INCH = 'Inch';
    
    const TYPE_FOOT = 'Foot';
    
    const AREA_SQUARE = 'square';
    
    const AREA_TRIANGLE = 'triangle';
    
    const AREA_SPHERE = 'sphere';
    
    const AREA_RECTANGLE = 'rectangle';
    
    const AREA_LENGTH = 'length';
    
    const AREA_ELLIPSE = 'ellipse';
    
    const AREA_CIRCLE = 'circle';
    
    const AREA_QUADRILATERAL = 'quadrilateral';
    
    const AREA_POLYGON = 'polygon';

    protected $product;
    
    protected $parentProduct;

    /**
     * @var \Magento\Framework\Registry
     */

    protected $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */

    protected $request;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyCode;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\App\RequestInterface $request,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param \Magento\Checkout\Model\Session $checkoutSession,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory,
     * @param \Magento\Catalog\Model\ProductFactory $productFactory,
     * @param \Magento\Tax\Helper\Data $taxHelper,
     * @param \Magento\Tax\Model\Calculation $taxCalculation,
     * @param \Magento\Catalog\Helper\Data $catalogHelper,
     */

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Catalog\Helper\Data $catalogHelper
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->currencyCode = $currencyFactory->create();
        $this->productFactory = $productFactory;
        $this->taxHelper = $taxHelper;
        $this->taxCalculation = $taxCalculation;
        $this->catalogHelper = $catalogHelper;
    }


    public function toOptionArray()
    {
        return [
            '' => __('-- Please Select --'),
            self::TYPE_MM     => __('Millimeter'),
            self::TYPE_CM     => __('Centimeter'),
            self::TYPE_M      => __('Meter'),
            self::TYPE_INCH   => __('Inch'),
            self::TYPE_FOOT   => __('Foot'),
        ];
    }
    public function getSqUnitName($key)
    {
          
        $_data  = [
            self::TYPE_MM     => __('sqmm'),
            self::TYPE_CM     => __('sqcm'),
            self::TYPE_M      => __('sqm'),
            self::TYPE_INCH   => __('sqin'),
            self::TYPE_FOOT   => __('sqft'),
        ];
        
        if (isset($_data[$key])) {
            return $_data[$key];
        }
        
        return $key;
    }

    public function getVolUnitName($key)
    {
          
        $_data  = [
            self::TYPE_MM     => __('cubic mm'),
            self::TYPE_CM     => __('cubic cm'),
            self::TYPE_M      => __('cubic meter'),
            self::TYPE_INCH   => __('cubic inch'),
            self::TYPE_FOOT   => __('cubic foot'),
        ];
        
        if (isset($_data[$key])) {
            return $_data[$key];
        }
        
        return $key;
    }

    public function getMeasurements()
    {
        
        return [
        
            self::TYPE_MM => [
                self::TYPE_MM    => 1,
                self::TYPE_CM    => 0.1000,
                self::TYPE_M     => 0.0010,
                self::TYPE_INCH  => 0.0393701,
                self::TYPE_FOOT  => 0.00328084,
            ],
            self::TYPE_CM => [
                self::TYPE_MM    => 10,
                self::TYPE_CM    => 1,
                self::TYPE_M     => 0.01,
                self::TYPE_INCH  => 0.393701,
                self::TYPE_FOOT  => 0.0328084,
            ],
            self::TYPE_M => [
                self::TYPE_MM    => 1000,
                self::TYPE_CM    => 100,
                self::TYPE_M     => 1,
                self::TYPE_INCH  => 39.3701,
                self::TYPE_FOOT  => 3.28084,
            ],
            self::TYPE_INCH => [
                self::TYPE_MM    => 25.4,
                self::TYPE_CM    => 2.54,
                self::TYPE_M     => 0.0254,
                self::TYPE_INCH  => 1,
                self::TYPE_FOOT  => 0.0833333,
            ],
            self::TYPE_FOOT => [
                self::TYPE_MM    => 304.8,
                self::TYPE_CM    => 30.48,
                self::TYPE_M     => 0.3048,
                self::TYPE_INCH  => 12,
                self::TYPE_FOOT  => 1,
            ],
        ];
    }

    public function toMillimeter($from, $value)
    {
         
         return $this->convert($from, self::TYPE_MM, $value);
    }
    
    public function toCentimeter($from, $value)
    {
        
        return $this->convert($from, self::TYPE_CM, $value);
    }
    
    public function toMeter($from, $value)
    {
        
        return $this->convert($from, self::TYPE_M, $value);
    }
    
    public function toInch($from, $value)
    {
        
         return $this->convert($from, self::TYPE_INCH, $value);
    }
    
    public function toFoot($from, $value)
    {
        
        return $this->convert($from, self::TYPE_FOOT, $value);
    }
    
    public function convert($from, $to, $value)
    {
        
        if (!$value) {
            return $value;
        }
         
         $meserments = $this->getMeasurements();
         
        if (isset($meserments[$from]) && isset($meserments[$from][$to])) {
            return ($value * $meserments[$from][$to]);
        }
         
         return $value;
    }

    public function setProduct($product)
    {
        $this->product = $this->productFactory->create()->load($product->getId());
        return $this;
    }
    
    public function getProduct()
    {
        
        if (!$this->product) {
            $this->product = $this->productFactory->create();
        }
        
        return $this->product;
    }
    
    public function setParentProduct($product)
    {
        
        if ($product && $product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $this->parentProduct = $product;
        }
        
        return $this;
    }
    
    public function getParentProduct()
    {
        
        return $this->parentProduct;
    }
    
    public function getDppcMinUnit()
    {
        
        if ($this->product->getDppcMinUnit() == null && $this->product->getDppcMaxUnit() == null) {
            if ($this->getParentProduct()) {
                return (float) $this->getParentProduct()->getDppcMinUnit();
            }
        }
        
        return (float) $this->product->getDppcMinUnit();
    }
    
    public function getDppcMaxUnit()
    {
        
        if ($this->product->getDppcMinUnit() == null && $this->product->getDppcMaxUnit() == null) {
            if ($this->getParentProduct()) {
                return (float) $this->getParentProduct()->getDppcMaxUnit();
            }
        }
        
        return (float) $this->product->getDppcMaxUnit();
    }
    
    public function getDppcInputUnit()
    {
         
        $inputUnit = $this->product->getDppcInputUnit();
        
        if ($inputUnit == null) {
            if ($this->getParentProduct()) {
                $inputUnit = $this->getParentProduct()->getDppcInputUnit();
            }
        }
        
        $meserments = $this->getMeasurements();
        
        if (isset($meserments[$inputUnit])) {
            return $inputUnit;
        }
         
        return self::TYPE_M;
    }
    
    public function getDppcOutputUnit()
    {
        
        $outputUnit = $this->product->getDppcOutputUnit();
        
        $meserments = $this->getMeasurements();
        
        if ($outputUnit == null) {
            if ($this->getParentProduct()) {
                $outputUnit = $this->getParentProduct()->getDppcOutputUnit();
            }
        }
        
        if (isset($meserments[$outputUnit])) {
            return $outputUnit;
        }
         
        return self::TYPE_M;
    }
    
    public function getDppcUnitPrice()
    {
        
        $price = $this->product->getDppcUnitPrice();
        
        if ($price == null) {
            if ($this->getParentProduct()) {
                $price = $this->getParentProduct()->getDppcUnitPrice();
            }
        }
        
        return (float) $price;
    }
    
    public function getDppcMinimumAreaPrice()
    {
        
        $price = $this->product->getDppcMinimumAreaPrice();
        
        if ($price == null) {
            if ($this->getParentProduct()) {
                $price = $this->getParentProduct()->getDppcMinimumAreaPrice();
            }
        }
        
        return (float) $price;
    }
    
    public function getDppcUnitPriceIncludesTax()
    {
        
        return $this->catalogHelper->getTaxPrice($this->getProduct(), $this->getDppcUnitPrice(), true);
    }
    
    public function getDppcUnitPriceExcludeTax()
    {
        
        return $this->catalogHelper->getTaxPrice($this->getProduct(), $this->getDppcUnitPrice(), false);
    }
    
    public function getTaxConfig()
    {

        if (!$this->taxCalculation->getCustomer() && $this->registry->registry('current_customer')) {
            $this->taxCalculation->setCustomer($this->registry->registry('current_customer'));
        }

        $_request = $this->taxCalculation->getDefaultRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $defaultTax = $this->taxCalculation->getRate($_request);

        $_request = $this->taxCalculation->getRateRequest();
        $_request->setProductClassId($this->getProduct()->getTaxClassId());
        $currentTax = $this->taxCalculation->getRate($_request);

        $taxConfig = [
            'includeTax'        => $this->taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $this->taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $this->taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => __('Incl. Tax')
        ];
        
        return $taxConfig;
    }
    
    public function getConfig()
    {
        
        $_config = [
            'unit'                  => $this->getMeasurements(),
            'unitPrice'             => $this->getDppcUnitPrice(),
            'minimumPrice'          => $this->getDppcMinimumAreaPrice(),
            'inputUnit'             => $this->getDppcInputUnit(),
            'outputUnit'            => $this->getDppcOutputUnit(),
            'minUnit'               => $this->getDppcMinUnit(),
            'maxUnit'               => $this->getDppcMaxUnit(),
            'unitPriceExcludeTax'   => $this->getDppcUnitPriceExcludeTax(),
            'unitPriceIncludeTax'   => $this->getDppcUnitPriceIncludesTax(),
            'taxConfig'             => $this->getTaxConfig(),
            'basePrice'             => (float) $this->getProduct()->getFinalPrice(),
            'outputSqUnitName'      => $this->getSqUnitName($this->getDppcOutputUnit()),
            'outputVolUnitName'     => $this->getVolUnitName($this->getDppcOutputUnit())
        ];
        
        if ($this->getProduct()->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            //if(Mage::helper('dppc')->isCpspEnabled()) {
                $_config['configurable'] =  $this->getConfigurableConfig($_config);
            //}
        }
         
        return $_config;
    }

    public function getConfigurableConfig($_default)
    {
        
        $_config = [];

        $parentProduct = $this->getProduct();

        if ($this->getProduct()->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            foreach ($this->getProduct()->getTypeInstance(true)->getUsedProducts($this->getProduct(), null) as $product) {
                if ($product->getDppcProductEnable()) {
                    $_childUnit = $this->setProduct($product)->setParentProduct($this->getProduct());
                       
                    $_config[$product->getId()] = $_childUnit->getConfig();
                } else {
                    $childFinalPrice = (float) $product->getFinalPrice();

                    $_default['basePrice'] = $childFinalPrice;

                    $_default['unitPriceExcludeTax'] = $this->setProduct($parentProduct)->getDppcUnitPriceExcludeTax();

                    $_default['unitPriceIncludeTax'] = $this->setProduct($parentProduct)->getDppcUnitPriceIncludesTax();

                    $_default['taxConfig'] = $this->setProduct($parentProduct)->getTaxConfig();

                    $_config[$product->getId()] = $_default;
                }
            }

            $this->setProduct($parentProduct);
        }
        
        return $_config;
    }
    
    public function convertProductUnit($value)
    {
        
        return $this->convert($this->getDppcInputUnit(), $this->getDppcOutputUnit(), $value);
    }
}
