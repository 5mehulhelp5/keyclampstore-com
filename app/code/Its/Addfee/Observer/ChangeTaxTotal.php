<?php
namespace Its\Addfee\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address\Total as T;
class ChangeTaxTotal implements ObserverInterface {
    function execute(Observer $observer) {
        $t = $observer->getData('total'); /** @var T $t */
		$q = $observer->getData('quote'); /** @var Q $q */
		if ($t->getAppliedTaxes()) {
			if ($tax = $q['fee_tax']) {
				$t->addTotalAmount('tax', $tax);
				$t->addBaseTotalAmount('tax', $tax);
				$t->setGrandTotal($t->getGrandTotal() + $tax);
				$t->setBaseGrandTotal($t->getBaseGrandTotal() + $tax);
			}
		}
        return $this;
    }
}