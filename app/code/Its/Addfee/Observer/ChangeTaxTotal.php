<?php
namespace Its\Addfee\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Address\Total as T;
final class ChangeTaxTotal implements ObserverInterface {
    function execute(O $o):void {
        $t = $o['total']; /** @var T $t */
		$q = $o['quote']; /** @var Q $q */
		if ($t->getAppliedTaxes() && ($tax = $q['fee_tax'])) {
			$t->addTotalAmount('tax', $tax);
			$t->addBaseTotalAmount('tax', $tax);
			$t->setGrandTotal($t->getGrandTotal() + $tax);
			$t->setBaseGrandTotal($t->getBaseGrandTotal() + $tax);
		}
    }
}