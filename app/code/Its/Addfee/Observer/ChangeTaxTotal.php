<?php
namespace Its\Addfee\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote\Address\Total as T;
class ChangeTaxTotal implements ObserverInterface {
    function execute(Observer $observer) {
        $t = $observer->getData('total'); /** @var T $t */
		/** @var \Magento\Quote\Model\Quote $q */
		$q = $observer->getData('quote');
        //make sure tax value exist
		if ($t->getAppliedTaxes()) {
			$tax = $q['fee_tax'];
			if ($tax) {
				$t->addTotalAmount('tax', $tax);
				$t->addBaseTotalAmount('tax', $tax);
				$t->setGrandTotal($t->getGrandTotal() + $tax);
				$t->setBaseGrandTotal($t->getBaseGrandTotal() + $tax);
			}
		}
        return $this;
    }
}