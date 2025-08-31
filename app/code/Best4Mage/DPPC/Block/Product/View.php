<?php

namespace Best4Mage\DPPC\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Best4Mage\DPPC\Model\ShapeFactory
     */
    protected $shapeFactory;

    /**
     * @var \Best4Mage\DPPC\Model\sideFactory
     */
    protected $sideFactory;

    /**
     * @var \Best4Mage\DPPC\Helper\Options
     */
    protected $optionHelper;

    /**
     * @var \Best4Mage\DPPC\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface|\Magento\Framework\Pricing\PriceCurrencyInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Best4Mage\DPPC\Model\ShapeFactory $shapeFactory
     * @param \Best4Mage\DPPC\Model\SideFactory $sideFactory
     * @param array $data
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */

	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Best4Mage\DPPC\Model\ShapeFactory $shapeFactory,
        \Best4Mage\DPPC\Model\SideFactory $sideFactory,
        \Best4Mage\DPPC\Helper\Options $optionHelper,
        \Best4Mage\DPPC\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->shapeFactory = $shapeFactory;
		$this->sideFactory = $sideFactory;
        $this->optionHelper = $optionHelper;
		$this->dataHelper = $dataHelper;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }


	public function isDppcEnable()
	{
		$product = $this->getProduct();

		return (bool)(int) $product->getDppcProductEnable();
	}

    public function getDppcHelper(){

        return $this->dataHelper;
    }

	public function getPriceFormat($amount){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
	    $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); // Instance of Pricing Helper
	    $price =  $amount; //Your Price
	    return $formattedPrice = $priceHelper->currency($price, true, false);
	}

	public function getProductShapes(){
		$productShapes = $this->getProduct()->getDppcProductShapes();

		if($productShapes != null || $productShapes != ''){
			$shapes = array();
			
			$dppcShapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('shape_id', ['in' => $productShapes])->setOrder('sort_order','ASC')->addFieldToFilter('status',1);
			
			if(count($dppcShapeCollection) > 0){
				foreach ($dppcShapeCollection as $shape) {
					$shapes[$shape->getId()] = $shape->getTitle();
				}
				return $shapes;
			}
		}
		return false;
	}

    public function getShapeSides(){
        $productShapes = $this->getProduct()->getDppcProductShapes();

        if($productShapes != null || $productShapes != ''){
            $sides = array();
            
            $dppcShapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('shape_id', ['in' => $productShapes]);
           
            if(count($dppcShapeCollection) > 0){
                foreach ($dppcShapeCollection as $shape) {
                    $shapeSides = explode(',', $shape->getSideId());

                    if(count($shapeSides) > 0){
                        foreach ($shapeSides as $side) {
                            $dppcSide = $this->sideFactory->create()->load($side,'code');
                            $sides[$shape->getId()][$side] = $dppcSide->getTitle();
                        }
                    }
                }
                return $sides;
            }
        }
        return false;
    }

    public function getMinMaxValues(){
        $productShapes = $this->getProduct()->getDppcProductShapes();

        if($productShapes != null || $productShapes != ''){
            $minmaxVal = array();
            
            $dppcShapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('shape_id', ['in' => $productShapes]);
           
            if(count($dppcShapeCollection) > 0){
                foreach ($dppcShapeCollection as $shape) {
                    $sides = explode(',',$shape->getSideId());
                    if($shape->getMinMaxValue()){
                        $minMaxValues = explode(',',$shape->getMinMaxValue());
                        foreach ($sides as $key => $sideId) {
                            $minMax = explode(':',$minMaxValues[$key]);
                            $minmaxVal[$shape->getId()][$sideId]['min_value'] = $minMax[0];
                            $minmaxVal[$shape->getId()][$sideId]['max_value'] = $minMax[1];
                        }
                    }
                }
                return $minmaxVal;
            }
        }
        return false;
    }

    public function getShapeCalculationType(){
        $productShapes = $this->getProduct()->getDppcProductShapes();

        if($productShapes != null || $productShapes != ''){
            $calType = array();
            
            $dppcShapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('shape_id', ['in' => $productShapes]);
           
            if(count($dppcShapeCollection) > 0){
                foreach ($dppcShapeCollection as $shape) {                    
                    $calculationType = $shape->getCalculationType();

                    $calType[$shape->getId()] = $calculationType;
                }
                return $calType;
            }
        }
        return false;
    }


    public function getShapeFormula(){
        $productShapes = $this->getProduct()->getDppcProductShapes();

        if($productShapes != null || $productShapes != ''){
            $formula = array();
            
            $dppcShapeCollection = $this->shapeFactory->create()->getCollection()->addFieldToFilter('shape_id', ['in' => $productShapes]);
            
            if(count($dppcShapeCollection) > 0){
                foreach ($dppcShapeCollection as $shape) {
                    $formula[$shape->getId()] = $shape->getFormula();
                }
                return $formula;
            }
        }
        return false;
    }

	public function getShapeImage($shapeId){

		$shapeImage = $this->shapeFactory->create()->load($shapeId)->getImage();
		if($shapeImage != null || $shapeImage != ''){
			return $this->getViewFileUrl('Best4Mage_DPPC::images/'.$shapeImage);
		}
		return false;
	}

	public function getDppcJsonConfig() 
    {
        $config = array();

        $DppcHelper = $this->getDppcHelper();
                
        $config = $this->optionHelper->setProduct($this->getProduct())->getConfig();
        
        $config['shapes'] = $this->getProductShapes();

        $config['sides'] = $this->getShapeSides();

        $config['formula'] = $this->getShapeFormula();

        $config['optionsPrice'] = $this->getJsonConfig();

        $config['minMax'] = $this->getMinMaxValues();

        $config['calculationType'] = $this->getShapeCalculationType();

        $config['configurePrice'] = $DppcHelper->configurePrice();

        return $this->_jsonEncoder->encode($config);
    } 

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if($this->hasData('product')){
            return $this->getData('product');
        }
        if(!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    public function getShapeById($shapeId){
        $shapeTitle = $this->shapeFactory->create()->load($shapeId)->getTitle();
        if($shapeTitle != null || $shapeTitle != ''){
            return $shapeTitle;
        }
        return false;
    }

    public function getSideByCode($side){
        $sideTitle = $this->sideFactory->create()->load($side,'code')->getTitle();
        if($sideTitle != null || $sideTitle != ''){
            return $sideTitle;
        }
        return false;
    }
}
