<?php
namespace Its\Addfee\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Add Weee item to Payment Cart amount.
 */
class PaymentCartCollectItemsAndAmounts implements ObserverInterface {

    /**
     * Add FPT amount as custom item to payment cart totals.
     *
     * @param Observer $observer
     * @return void
     */
    function execute(Observer $observer) {
        /** @var \Magento\Payment\Model\Cart $c */
        $c = $observer->getEvent()->getCart();
		if ($c instanceof \Magento\Paypal\Model\Cart) {
			/** @var \Magento\Payment\Model\Cart\SalesModel\Quote $m */
			$m = $c->getSalesModel();
			/** @var \Magento\Quote\Model\Quote $q */
			$q = $m->getTaxContainer()->getQuote();
			if ($q['fee']) {
				$c->addCustomItem('Powdercoating Fee', 1, $q['fee']);
				/** @used-by \Magento\Paypal\Model\Cart::_importItemsFromSalesModel() */
				$q->setBaseTaxAmount($q->getBaseTaxAmount() + $q['fee_tax']);
			}
			$v = true;
		}
    }
}