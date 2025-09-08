<?php
namespace Its\Addfee\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
class ChangeTaxTotal implements ObserverInterface {
    function execute(Observer $observer) {
        /** @var Magento\Quote\Model\Quote\Address\Total */
        $total = $observer->getData('total');
		/** @var \Magento\Quote\Model\Quote $q */
		$q = $observer->getData('quote');
        //make sure tax value exist
		if ($total->getAppliedTaxes()) {
			$tax = $q['fee_tax'];
			if ($tax) {
				$total->addTotalAmount('tax', $tax);
				$total->addBaseTotalAmount('tax', $tax);
				$total->setGrandTotal($total->getGrandTotal() + $tax);
				$total->setBaseGrandTotal($total->getBaseGrandTotal() + $tax);
			}
		}
        return $this;
    }
}