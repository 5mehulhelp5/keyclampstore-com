<?php
declare(strict_types=1);

namespace Its\Addfee\Observer\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
	
	protected $_checkoutSession;

	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Pricing\Helper\Data $pricingHelper
	){
		$this->_checkoutSession = $checkoutSession;
		$this->pricingHelper = $pricingHelper;
	}

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $order = $observer->getOrder();
        $orderId = $order->getRealOrderId();
		$colorcode = $this->_checkoutSession->getIsSetPowderCoatingColor();
		if (!empty($colorcode)) {
			$items = $this->_checkoutSession->getIsSetPowderCoatingItems();
			$order->setPowdercoatingColorcode($colorcode);
			$order->setPowdercoatingItems($items);

            $extrafee = $this->pricingHelper->currencyByStore($order->getFee(), $order->getStoreId(), true, false);

            $skus = '';
            if (!empty($items)) {
                $skus = implode(',', unserialize($items));
            };
		    $order->addStatusHistoryComment(__('Powdercoating selected color: %1 and fee is %2 and items are %3',
                    $colorcode,
                    $extrafee,
                    $skus
            ));
		}
		$this->_checkoutSession->unsIsSetPowderCoating(0);	
		$this->_checkoutSession->unsIsSetPowderCoatingColor(null);	
		$this->_checkoutSession->unsIsSetPowderCoatingItems(null);	
		$order->save();
    }
}

