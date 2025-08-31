<?php
namespace Its\Addfee\Model\Total;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null; 

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
								\Magento\Checkout\Model\Session $checkoutSession,
								\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
								\Magento\Catalog\Model\Product $catalogProduct)
    {
        $this->quoteValidator = $quoteValidator;
		$this->checkoutSession = $checkoutSession;
		$this->catalogProduct = $catalogProduct;
		$this->scopeConfigInterface = $scopeConfigInterface;
    }
  public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);


        $exist_amount = 0; //$quote->getFee(); 
        $fee = $this->getFinalFeeAmount($quote);; //Excellence_Fee_Model_Fee::getFee();
        $balance = $fee - $exist_amount;

        $total->setTotalAmount('fee', $balance);
        $total->setBaseTotalAmount('fee', $balance);

        $total->setFee($balance);
        $total->setBaseFee($balance);

        //$total->setGrandTotal($total->getGrandTotal() + $balance);
        //$total->setBaseGrandTotal($total->getBaseGrandTotal() + $balance);
		
		$total->setGrandTotal($total->getGrandTotal());
		$total->setBaseGrandTotal($total->getBaseGrandTotal());


        return $this;
    } 
	
	public function getFinalFeeAmount(\Magento\Quote\Model\Quote $quote)
	{
		
		$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/getFinalFeeAmount.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("ERROR--"); 
		
		$fee = 0; 
		
		if(null !== $this->checkoutSession->getIsSetPowderCoating() && $this->checkoutSession->getIsSetPowderCoating()=="1"){
						
		} else {
			return 0;
		}
		
		$powercoatingItemsTemp = $this->checkoutSession->getIsSetPowderCoatingItems();
		$powercoatingItems = [];		
		$powercoatingItems = unserialize($powercoatingItemsTemp);
		
		$items = $quote->getAllVisibleItems();
		$overall_price_for_powdder_coating = 0;
		$discount_percentage_flag = 0;
		
		$isPowederCoatingCart = 0; 
		
		/*if($this->checkoutSession->getSubscriptionIdData()){
			$discount_percentage_flag = 1;
		}*/
		//$subscription_id_auto_order_generate = $this->checkoutSession->getSubscriptionIdData();
		foreach($items as $item){
			
			if(in_array($item->getProductId(),$powercoatingItems)){
			} else {
				continue;
			}
			
			$buyInfo = $item->getBuyRequest();
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
			
				//$overall_price_for_powdder_coating += $_product->getPowder_coating_fee() * $item->getQty();
				$overall_price_for_powdder_coating += $finalPwdCtPrice * $item->getQty();
				$isPowederCoatingCart = 1;
			}
			$_product->reset();
		}	

		if($isPowederCoatingCart == 1){
			if($overall_price_for_powdder_coating < 70 ){
				$overall_price_for_powdder_coating = 70;
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
		$this->checkoutSession->setPowderCoatingPrice($fee);
		return $fee;
	}

    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
		$finalFeeamount = $this->getFinalFeeAmount($quote);
		
		$writer = new \Zend\Log\Writer\Stream(BP.'/var/log/getFinalFeeAmount.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("Final--"); 
		$logger->info($finalFeeamount); 
		//$this->checkoutSession->setPowderCoatingPrice($finalFeeamount);
		 
        return [
            'code' => 'fee',
            'title' => __("Fee"),
            'value' => $finalFeeamount
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Fee');
    }
}
?>