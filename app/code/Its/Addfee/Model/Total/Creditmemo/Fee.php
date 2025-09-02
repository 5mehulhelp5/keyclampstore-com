<?php
namespace Its\Addfee\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
class Fee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\creditmemo $creditmemo
     * @return $this
     */
    function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {		
        $amount = $creditmemo->getOrder()->getFee();	
        $creditmemo->setFee($amount);
        $creditmemo->setBaseFee($amount);
        // $creditmemo->setBaseGrandTotal(0)
        //                 ->setGrandTotal(0)
        //                 ->setAllowZeroGrandTotal(true);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount);		
        return $this;
    }
}