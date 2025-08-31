<?php

namespace Best4Mage\DPPC\Model\ResourceModel\Shape;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'shape_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Best4Mage\DPPC\Model\Shape', 'Best4Mage\DPPC\Model\ResourceModel\Shape');
    }
}
