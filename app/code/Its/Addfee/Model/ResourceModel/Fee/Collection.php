<?php
namespace Its\Addfee\Model\ResourceModel\Fee;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    function _construct()
    {
        $this->_init('Its\Addfee\Model\Fee', 'Its\Addfee\Model\ResourceModel\Fee');
    }
}
