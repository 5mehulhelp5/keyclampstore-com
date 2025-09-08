<?php
namespace Its\Addfee\Observer;
use Magento\Checkout\Model\Session as S;
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote as Q;
class PaymentCartCollectItemsAndAmounts implements ObserverInterface {
    /**
	 * 2025-09-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor the `Its_Addfee` module": https://github.com/keyclampstore-com/m/issues/4
	 * 2) `payment_cart_collect_items_and_amounts`
     * @param Observer $observer
     * @return void
     */
    function execute(Observer $observer) {
        /** @var \Magento\Payment\Model\Cart $c */
        $c = $observer->getEvent()->getCart();
		if ($c instanceof \Magento\Paypal\Model\Cart) {
			# 2025-09-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Trying to access array offset on value of type null
			# in app/code/Its/Addfee/Observer/PaymentCartCollectItemsAndAmounts.php on line 25»:
			# https://github.com/keyclampstore-com/m/issues/5
			$s = OM::getInstance()->get(S::class); /** @var S $s */
			$q = $s->getQuote(); /** @var Q $q */
			if ($q['fee']) {
				$c->addCustomItem('Powdercoating Fee', 1, $q['fee']);
				/** @used-by \Magento\Paypal\Model\Cart::_importItemsFromSalesModel() */
				$q->setBaseTaxAmount($q->getBaseTaxAmount() + $q['fee_tax']);
			}
		}
    }
}