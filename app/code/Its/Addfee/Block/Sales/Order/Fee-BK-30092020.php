<?php
namespace Its\Addfee\Block\Sales\Order;



class Fee extends \Magento\Framework\View\Element\Template
{
    /**
     * Tax configuration model
     *
     * @var \Magento\Tax\Model\Config
     */
    protected $_config;

    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;
    protected $_itsfeeModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Its\Addfee\Model\Fee $itsfeeModel,
		\Magento\Catalog\Model\Product $catalogProduct,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->_itsfeeModel = $itsfeeModel;
		$this->catalogProduct = $catalogProduct;
		$this->scopeConfigInterface = $scopeConfigInterface;
        parent::__construct($context, $data);
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    } 
    public function getStore()
    {
        return $this->_order->getStore();
    }

      /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
     public function initTotals()
    {

        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        $store = $this->getStore();
		
		$_itsfee = 0;
		$feeData = $this->_itsfeeModel->getCollection()->addFieldToFilter('orderno',['eq' => $this->_order->getIncrementId()]);
		foreach($feeData as $fee){
			//print_r($a->getData());
			$_itsfee = $fee->getFee();
			//echo "hi";
			break;
		}
		
		////////////
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$order = $objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($this->_order->getIncrementId()); 
		$_checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session'); 
		if($_checkoutSession->getIsSetPowderCoating()==1) {
			if($_itsfee==0){
				$_itsfee = $this->getFinalFeeAmountCustom($order);			
			}
		}
		////////////
		
        $fee = new \Magento\Framework\DataObject(
                [
                    'code' => 'fee',
                    'strong' => false,
                    'value' => $_itsfee,                  
                    'label' => __('Fee'),
                ]
            );
            $parent->addTotal($fee, 'fee');         
            $parent->addTotal($fee, 'fee');
            return $this;
    }
	
	public function getFinalFeeAmountCustom($order)
	{
		$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/getFinalFeeAmountBlobk.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("ERROR--"); 
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$_checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session'); 
		
		$powercoatingItemsTemp = $_checkoutSession->getIsSetPowderCoatingItems();
		$powercoatingItemsColorCode = $_checkoutSession->getIsSetPowderCoatingColor();
		$powercoatingItems = [];		
		$powercoatingItems = unserialize($powercoatingItemsTemp);
		
		$fee = 0; 
		
		$isPowederCoatingCart = 0;
		
		$items = $order->getAllVisibleItems();
		$overall_price_for_powdder_coating = 0;
		$discount_percentage_flag = 0;
		 
		foreach($items as $item){ 
			if(in_array($item->getSku(),$powercoatingItems)){
			} else {
				continue;
			}
			$logger->info($item->getProduct()->getId());
			
			$_product = $this->catalogProduct->load($item->getProduct()->getId());
			$logger->info($_product->getPowder_coating_available());  
			$logger->info($_product->getPowder_coating_fee());  
			
			
			if($_product->getPowder_coating_available()){
				/* start - get custom option powdercoating price */
				$feeArr = [];
				$feeArr = explode("|",$_product->getPowder_coating_fee());
				//echo $item->getSku();
				//print_r($feeArr);exit;
				$finalPwdCtPrice = 0;
				foreach($feeArr as $fees){
					$feesArr = explode(":",$fees);			
					if($item->getSku() == $feesArr[0]){
						if($feesArr[1]==1){
							$finalPwdCtPrice = $feesArr[2];
						}
					}
				}
				if($finalPwdCtPrice==0){
					//$finalPwdCtPrice = 5000000;
				}
				/* end - get custom option powdercoating price */
				
				//$overall_price_for_powdder_coating += $_product->getPowder_coating_fee() * $item->getQtyOrdered();
				$overall_price_for_powdder_coating += $finalPwdCtPrice * $item->getQtyOrdered();
				$isPowederCoatingCart = 1;
			}
		}	
		
		$minimumPowderCoatingFeeColor0 = ["RAL1023","RAL9011","RAL9003"]; 
		$minimumPowderCoatingFeeColor50 = ["RAL7016","RAL5005","RAL3020","RAL6002"];
		
		if($isPowederCoatingCart == 1){
			//if($overall_price_for_powdder_coating < 70 ){
			if($overall_price_for_powdder_coating ==0 ){
				//$overall_price_for_powdder_coating = 70;
				
				if(in_array($powercoatingItemsColorCode,$minimumPowderCoatingFeeColor0)){
					//$overall_price_for_powdder_coating = 0; 
				} else if(in_array($powercoatingItemsColorCode,$minimumPowderCoatingFeeColor50)){
					$overall_price_for_powdder_coating = 50;
				} else {
					$overall_price_for_powdder_coating = 70;
				}
			}
		}	
		
		//$discontArr = explode("|","200:5|500:10|1000:20");
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		$discontArr = explode("|",$this->scopeConfigInterface->getValue("itsfee/general/powdercotaingprices", $storeScope));
		
		$finalDiscPerce = 0;
		foreach($discontArr as $discArr){
			$discontTemp = explode(":",$discArr);			
			if($overall_price_for_powdder_coating>=$discontTemp[0]){
				$finalDiscPerce = $discontTemp[1];
			}
		}
		if($finalDiscPerce>0){
			$overall_price_for_powdder_coating = $overall_price_for_powdder_coating - ($overall_price_for_powdder_coating*$finalDiscPerce/100);
		}
		
		$logger->info($overall_price_for_powdder_coating); 
		$fee = round($overall_price_for_powdder_coating,2);
		
		return $fee;
	}
	
	public function saveOrderComments($orderId)
    {
		$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/saveOrderComments.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("ERROR--"); 
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$order = $objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderId); 
		$_checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session'); 
		 
 
		try {
			if($_checkoutSession->getIsSetPowderCoating()==1) {
				$logger->info("ERROR--"); 
				//$order->addStatusToHistory($order->getStatus(), "Powdercoating selected color : ".$_checkoutSession->getIsSetPowderCoatingColor()." and fee is ".$_checkoutSession->getPowderCoatingPrice(), false);
				//$order->save();
				
				
				//$history = $order->addStatusHistoryComment("Powdercoating selected color : ".$_checkoutSession->getIsSetPowderCoatingColor()." and fee is ".$_checkoutSession->getPowderCoatingPrice());
				//$history = $order->addStatusHistoryComment("Powdercoating selected color : ".$_checkoutSession->getIsSetPowderCoatingColor()." and fee is ".$this->getFinalFeeAmountCustom($order));
				//$history->save();
				
				$powercoatingItemsTemp = $_checkoutSession->getIsSetPowderCoatingItems();
				$powercoatingItems = [];		
				$powercoatingItems = unserialize($powercoatingItemsTemp);
				
				
				
				
				/////////////////////////
				$items = $order->getAllVisibleItems();
				$powdderitemsArr = []; 
				foreach($items as $item){ 
					if(in_array($item->getSku(),$powercoatingItems)){
						$powdderitemsArr[] = $item->getSku();
					} else {
						continue;
					}
				}
				$powercoatingItemsStr = implode(", ",$powdderitemsArr);
				//$powercoatingItemsStr = "";

				/////////////////////////
				$feeItems = $this->getFinalFeeAmountCustom($order);
		
				$commentToOrder = "Powdercoating selected color : ".$_checkoutSession->getIsSetPowderCoatingColor()." and fee is ".$feeItems." and items are ".$powercoatingItemsStr;
				$order->addCommentToStatusHistory($commentToOrder,false,true  );
				$order->save();
				 
				$this->_itsfeeModel->setData('orderno', $order->getIncrementId()); 
				//$this->_itsfeeModel->setData('fee', $this->getFinalFeeAmountCustom($order)); 
				$this->_itsfeeModel->setData('fee', $feeItems); 
				$this->_itsfeeModel->save(); 
				
				$_checkoutSession->setIsSetPowderCoating(0);	
				$_checkoutSession->setIsSetPowderCoatingColor("");	
				$_checkoutSession->setPowderCoatingPrice("");	
				$_checkoutSession->setIsSetPowderCoatingItems("");	
			}
			//$order->save();
		} catch (\Exception $exception) {
			 
			$logger->info($exception->getMessage()); 
		}
		
		 
		
	}

}
?>