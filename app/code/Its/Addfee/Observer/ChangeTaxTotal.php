<?php
namespace Its\Addfee\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;

class ChangeTaxTotal implements ObserverInterface
{
    public $additionalTaxAmt = 20;

    function execute(Observer $observer)
    {
        /** @var Magento\Quote\Model\Quote\Address\Total */
        $total = $observer->getData('total');

        //make sure tax value exist
        if ($total->getAppliedTaxes()) {
            $total->addTotalAmount('tax', $total->getFee() - $total->getBaseFee());
           $total->addBaseTotalAmount('tax', $total->getFee() - $total->getBaseFee());
         }

        return $this;
    }
}