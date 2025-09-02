<?php
namespace Its\Addfee\Observer\Sales;

class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
           
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $order->setData('fee', $quote->getData('fee'));
            $order->setData('base_fee', $quote->getData('base_fee'));

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}