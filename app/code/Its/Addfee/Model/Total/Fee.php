<?php
namespace Its\Addfee\Model\Total;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
	public $additionalTaxAmt = 20;

	public $colorcode;

   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null; 

    function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
								\Magento\Checkout\Model\Session $checkoutSession,
								\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
								\Magento\Catalog\Model\Product $catalogProduct)
    {
        $this->quoteValidator = $quoteValidator;
		$this->checkoutSession = $checkoutSession;
		$this->catalogProduct = $catalogProduct;
		$this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * @return $this
     */
  	function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $t
    ) {
        parent::collect($quote, $shippingAssignment, $t);
		# 2025-09-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Repair the payment process when a powder coating fee is applied":
		# https://github.com/keyclampstore-com/m/issues/3
		$add = function(string $k, float $v) use($t) {
			$t[$k] = $t[$k] + $v;
			$t["base_$k"] = $t["base_$k"] + $v;
		};
		if ($shippingAssignment->getItems()) {
			$fee = $this->getFinalFeeAmount($quote);
			$tax = round(0.2 * $fee, 2);
			//$fee = $basefee + ($basefee * $this->additionalTaxAmt / 100);
		//	$add('subtotal', $fee);
		//	$add('subtotal_with_discount', $fee);
			$add('tax_amount', $tax);
		//	$add('subtotal_incl_tax', $fee + $tax);
			$t->addTotalAmount('tax', $tax);
			$t->addBaseTotalAmount('tax', $tax);
			$t->setTotalAmount($this->getCode(), $fee);
			$t->setBaseTotalAmount($this->getCode(), $fee);
			/** @used-by \Its\Addfee\Observer\Sales\QuoteSubmitBefore::execute */
			/** @used-by \Its\Addfee\Observer\PaymentCartCollectItemsAndAmounts::execute() */
			$quote->setFee($fee);
			$quote->setBaseFee($fee);
			/** @used-by \Its\Addfee\Observer\PaymentCartCollectItemsAndAmounts::execute() */
			/** @used-by \Its\Addfee\Observer\ChangeTaxTotal::execute() */
			$quote->setFeeTax($tax);
		}
        return $this;
    }


	/**
	 * 2025-09-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Its_Addfee` module": https://github.com/keyclampstore-com/m/issues/4
	 * @used-by self::collect()
	 * @used-by self::fetch()
	 */
	private function getFinalFeeAmount(\Magento\Quote\Model\Quote $quote): float {
		$fee = 0; 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();		
		$checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
		$customerSession = $objectManager->create('\Magento\Checkout\Model\Session');
		
		
		if(null !== $checkoutSession->getIsSetPowderCoating() && $checkoutSession->getIsSetPowderCoating()=="1"){

		} else {
			return 0;
		}
		
		$powercoatingItemsTemp = $checkoutSession->getIsSetPowderCoatingItems();
		$powercoatingItemsColorCode = $checkoutSession->getIsSetPowderCoatingColor();
		$powercoatingItems = [];
		$powercoatingItems = unserialize($powercoatingItemsTemp);

		$this->colorcode = $powercoatingItemsColorCode;
		
		$items = $quote->getAllVisibleItems();
		$overall_price_for_powdder_coating = 0;
		$discount_percentage_flag = 0;
		
		$isPowederCoatingCart = 0; 
		
		/*if($this->checkoutSession->getSubscriptionIdData()){
			$discount_percentage_flag = 1;
		}*/
		//$subscription_id_auto_order_generate = $this->checkoutSession->getSubscriptionIdData();
		foreach($items as $item){
			
			if(in_array($item->getSku(),$powercoatingItems)){
			} else {
				continue;
			}
			
			$buyInfo = $item->getBuyRequest();			
			$_product = $this->catalogProduct->load($item->getProduct()->getId());
			
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

				//$overall_price_for_powdder_coating += $_product->getPowder_coating_fee() * $item->getQty();
				$overall_price_for_powdder_coating += $finalPwdCtPrice * $item->getQty();
				$isPowederCoatingCart = 1;
			}
			$_product->reset();
		}	
		
		$minimumPowderCoatingFeeColor0 = ["RAL1023","RAL9005"]; 
		$minimumPowderCoatingFeeColor50 = ["RAL7016","RAL5005","RAL3020","RAL6002","RAL9016"];

		if($isPowederCoatingCart == 1){
			
			if($overall_price_for_powdder_coating < 70 ){ 			
				//$overall_price_for_powdder_coating = 70;
				
				if(in_array($powercoatingItemsColorCode,$minimumPowderCoatingFeeColor0)){
					//$overall_price_for_powdder_coating = 0; 
				} else if(in_array($powercoatingItemsColorCode,$minimumPowderCoatingFeeColor50)){
					if($overall_price_for_powdder_coating < 70 ) {
						$overall_price_for_powdder_coating = 70;
					}
				} else { 
					if($overall_price_for_powdder_coating < 90 ) {
						$overall_price_for_powdder_coating = 90;
					}
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
		
		$fee = round($overall_price_for_powdder_coating,2);
		$checkoutSession->setPowderCoatingPrice($fee);
		$customerSession->setPowderCoatingPrice($fee);
		$quote->setTestfeeamount($fee);
		$v = 50 / 1.2;
		if ($fee > 0 &&  $fee < $v && !in_array($this->colorcode,["RAL1023","RAL9005"])) {
			$fee = $v;
		}
		return round($fee, 2);
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
     * Assign subtotal amount and label to address object
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function fetch(\Magento\Quote\Model\Quote $q, \Magento\Quote\Model\Quote\Address\Total $total) {
        return [
            'code' => 'fee',
            'title' => ('Powdercoating Fee'),
            'value' => $this->getFinalFeeAmount($q)
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    function getLabel()
    {
        return __('Powdercoating Fee');
    }
}
