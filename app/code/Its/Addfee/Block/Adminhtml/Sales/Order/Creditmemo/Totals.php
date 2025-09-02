<?php
namespace Its\Addfee\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @return mixed
     */
    function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return mixed
     */
    function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     * @return $this
     */
    function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();

        if(!$this->getSource()->getFee()) {
            return $this;
        }

        $fee = new \Magento\Framework\DataObject(
            [
                'code' => 'fee',
                'strong' => false,
                'value' => $this->getParentBlock()->getOrder()->getFee(),
                'label' => __("Fee"),
            ]
        );

        $this->getParentBlock()->addTotalBefore($fee, 'grand_total');

        return $this;
    }
}