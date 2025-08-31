<?php
namespace Its\Addfee\Model\Sales\Total\Quote;
class Tax extends \Magento\Tax\Model\Sales\Total\Quote\Tax
{
    /**
    * Custom Collect tax totals for quote address
    *
    * @param Quote $quote
    * @param ShippingAssignmentInterface $shippingAssignment
    * @param Address\Total $total
    * @return $this
    */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
  //      echo $total->getTaxAmount(); //die;
//$set_your_tax_here=222;
       /* your calculation here goes here */
       // $total->setTaxAmount($set_your_tax_here);

        return $this;
    }

}