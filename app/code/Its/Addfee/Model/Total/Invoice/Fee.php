<?php
namespace Its\Addfee\Model\Total\Invoice;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
class Fee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        // $invoice->setFee($invoice->getOrder()->getFee());
        // $invoice->setBaseFee(0);
        $amount = $invoice->getOrder()->getFee();
        $invoice->setFee($amount);
        // $invoice->setBaseFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $amount);
        return $this;
    }
}