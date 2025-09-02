<?php
namespace Its\Addfee\Block\Sales\Order;

class Fee extends \Magento\Framework\View\Element\Template
{
    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    function getSource()
    {
        return $this->_source;
    }

    /**
     * @return mixed
     */
    function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * @return Order
     */
    function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $fee = new \Magento\Framework\DataObject(
                [
                    'code' => 'fee',
                    'strong' => false,
                    'value' => $this->_order->getFee(),                  
                    'label' => __('Powdercoating Fee'),
                ]

            );

        $parent->addTotal($fee, 'fee');         
        $parent->addTotal($fee, 'fee');
        
        return $this;
    }
}
